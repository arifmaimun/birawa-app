# Dokumentasi Teknis Birawa Hub (Super Admin Panel)

**Versi Dokumen:** 1.0.0  
**Tanggal:** 2026-01-02  
**Status:** Confidential  
**Target Pembaca:** System Architects, Security Engineers, Super Administrators

---

## 1. Deskripsi Sistem

**Birawa Hub** adalah platform manajemen pusat yang dirancang secara eksklusif untuk peran **Super Admin**. Platform ini berfungsi sebagai pusat komando (command center) untuk seluruh ekosistem aplikasi Birawa, memfasilitasi manajemen sistem tingkat tinggi, konfigurasi keamanan global, dan pemantauan aktivitas sistem secara real-time.

Berbeda dengan panel operasional (seperti panel dokter atau kasir), Birawa Hub memiliki akses penuh ke seluruh entitas data dan konfigurasi infrastruktur.

### 1.1 Fungsi Utama
*   **Manajemen Sistem Tingkat Tinggi**: Pengelolaan tenant, konfigurasi environment global, dan manajemen role/permission menggunakan RBAC (Role-Based Access Control) yang granular.
*   **Konfigurasi Keamanan Global**: Pengaturan kebijakan password, sesi, whitelist IP, dan rotasi kunci enkripsi.
*   **Monitoring Aktivitas Sistem**: Dashboard telemetri untuk memantau kesehatan server, antrian job (queue), dan audit log aktivitas pengguna yang mencurigakan.

### 1.2 Batasan Akses
Akses ke Birawa Hub dibatasi secara ketat hanya untuk pengguna dengan role `super_admin`.

*   **Fitur Terlarang untuk Non-Super Admin**:
    *   Modifikasi `Role` dan `Permission`.
    *   Akses ke menu `Audit Logs` dan `System Health`.
    *   Ekspor data sensitif dalam jumlah besar (Bulk Export).
    *   Konfigurasi plugin sistem (misal: Backup, Shield).
*   **Mekanisme Penolakan Akses**:
    *   Middleware `EnsureUserHasRole` akan memvalidasi role pengguna pada setiap request HTTP.
    *   Jika pengguna tanpa role `super_admin` mencoba mengakses route `/birawa-hub/*`, sistem akan merespons dengan HTTP 403 Forbidden dan mencatat insiden tersebut ke dalam security log.
    *   Panel tidak akan dirender sama sekali untuk user yang tidak berhak (metode `canAccessPanel` pada model User).

### 1.3 Arsitektur Sistem

Birawa Hub dibangun di atas arsitektur monolitik modular dengan filamentPHP sebagai kerangka kerja admin panel.

```mermaid
graph TD
    User[Super Admin] -->|HTTPS + MFA| WAF[Web Application Firewall]
    WAF -->|Filter Malicious Traffic| LB[Load Balancer]
    LB -->|Route Request| WebServer[Nginx Web Server]
    
    subgraph "Application Server (Birawa App)"
        WebServer -->|FastCGI| PHP[PHP-FPM (Laravel)]
        
        subgraph "Middleware Layer"
            PHP --> Auth[Authentication (Sanctum)]
            Auth --> RBAC[Role Check (Filament Shield)]
            RBAC --> Audit[Audit Logger]
        end
        
        subgraph "Core Components"
            Audit --> HubPanel[Birawa Hub PanelProvider]
            HubPanel --> UserMan[User Management]
            HubPanel --> SysConfig[System Config]
            HubPanel --> SecMon[Security Monitor]
        end
    end
    
    subgraph "Data Layer"
        UserMan --> DB[(PostgreSQL Database)]
        SysConfig --> Redis[(Redis Cache)]
    end
```

**Spesifikasi Infrastruktur:**
*   **Backend Framework**: Laravel 11 dengan FilamentPHP v3.
*   **Database**: PostgreSQL 16 (Primary) dengan replikasi untuk read-heavy operations.
*   **Caching**: Redis 7.0 untuk session storage dan cache data konfigurasi.
*   **Web Server**: Nginx dengan konfigurasi keamanan ketat (HSTS, CSP headers).

---

## 2. Persyaratan Akses

Akses ke Birawa Hub memerlukan tingkat verifikasi tertinggi dalam ekosistem.

### 2.1 Kriteria Pengguna
1.  **Role**: Wajib memiliki role `super_admin` yang ditetapkan langsung di database (tidak dapat diubah via UI standar).
2.  **Autentikasi Multi-Faktor (MFA)**: Wajib mengaktifkan 2FA (TOTP via Google Authenticator atau YubiKey).
3.  **IP Whitelist**: Akses hanya diizinkan dari alamat IP statis kantor pusat atau VPN perusahaan yang terdaftar.

### 2.2 Mekanisme Otorisasi Super Admin
Setiap sesi super admin bersifat sementara dan memerlukan re-validasi untuk operasi kritis.

**Flow Autentikasi:**
1.  User memasukkan email dan password.
2.  Sistem memvalidasi kredensial.
3.  Sistem meminta kode TOTP 6 digit.
4.  Setelah valid, sistem menerbitkan `access_token` (untuk API) atau sesi web terenkripsi.

**Sistem Approval Tambahan:**
Untuk operasi destruktif (misal: `DELETE` pada tabel user atau `DROP` tabel audit), sistem menerapkan mekanisme **"Sudo Mode"** dimana admin harus memasukkan ulang password mereka sebelum aksi dieksekusi.

### 2.3 Level Keamanan
*   **Enkripsi End-to-End**: Seluruh komunikasi menggunakan TLS 1.3. Data sensitif (PII, rekam medis) dienkripsi di database (At Rest) menggunakan AES-256-CBC.
*   **Intrusion Detection System (IDS)**: Monitor otomatis terhadap pola serangan umum (SQL Injection, XSS) dengan pemblokiran IP otomatis setelah 5 percobaan gagal (Rate Limiting).
*   **Brute Force Protection**: Login dibatasi maksimal 3 kali percobaan gagal per menit. Akun terkunci otomatis selama 15 menit setelah 5 kali gagal berturut-turut.

---

## 3. Panduan Penggunaan

### 3.1 Login Super Admin

1.  Akses URL: `https://[domain-app]/birawa-hub/login`.
2.  Masukkan Email korporat dan Password yang kuat (minimal 12 karakter, campuran simbol/angka).
3.  **Layar MFA**: Masukkan kode dari aplikasi Authenticator Anda.
    > *Troubleshooting*: Jika kode ditolak, pastikan jam perangkat Anda sinkron dengan waktu server (NTP).
4.  **Validasi**: Setelah berhasil, Anda akan diarahkan ke Dashboard dengan indikator "Super Admin" di pojok kanan atas.

### 3.2 Fitur Khusus Super Admin

| Menu | Deskripsi | Batasan Operasional |
| :--- | :--- | :--- |
| **User Management** | CRUD seluruh user, assign role, reset password. | Tidak bisa menghapus akun Super Admin utama (Root). |
| **Shield / Roles** | Konfigurasi permission granular per resource. | Perubahan permission memerlukan cache clear manual. |
| **Audit Logs** | Melihat riwayat aktivitas seluruh user. | Log bersifat *read-only* dan tidak dapat dihapus (immutable). |
| **Backups** | Manajemen backup database dan file. | Restore hanya boleh dilakukan saat maintenance window. |

### 3.3 Best Practices
*   **Manajemen Sesi**: Jangan pernah mencentang "Remember Me" pada perangkat publik. Sesi akan kadaluarsa otomatis setelah 30 menit inaktivitas.
*   **Logout**: Selalu gunakan tombol "Sign Out" di menu profil, jangan hanya menutup tab browser.
*   **Akses Simultan**: Sistem membatasi 1 sesi aktif per user. Login baru akan mematikan sesi sebelumnya.

---

## 4. Kebijakan Keamanan

### 4.1 Protokol Keamanan
*   **Rotasi Credential**: Password Super Admin wajib diganti setiap 90 hari. History 5 password terakhir disimpan untuk mencegah penggunaan ulang.
*   **Notifikasi Aktivitas**: Email otomatis dikirimkan ke tim keamanan jika terdeteksi login dari IP baru atau perangkat yang tidak dikenali.
*   **Lock Account**: Akun akan dikunci permanen jika terdeteksi pola akses anomali (misal: akses dari 2 negara berbeda dalam waktu 1 jam). Unlock hanya bisa dilakukan melalui akses database langsung oleh CTO.

### 4.2 Sistem Audit Trail
Setiap mutasi data (Create, Update, Delete) di Birawa Hub dicatat dalam tabel `audit_logs`.

*   **Format Log**:
    ```json
    {
      "user_id": 1,
      "event": "updated",
      "auditable_type": "App\\Models\\User",
      "auditable_id": 45,
      "old_values": {"role": "doctor"},
      "new_values": {"role": "super_admin"},
      "ip_address": "192.168.1.50",
      "user_agent": "Mozilla/5.0...",
      "created_at": "2026-01-02 10:00:00"
    }
    ```
*   **Retensi Data**: Log disimpan panas (hot storage) selama 1 tahun, kemudian diarsipkan ke Cold Storage (S3 Glacier) selama 5 tahun.
*   **Review**: Audit log direview setiap minggu oleh Security Officer.

### 4.3 Prosedur Darurat (Incident Response)
Jika terjadi indikasi peretasan pada akun Super Admin:

1.  **Isolasi**: Segera jalankan perintah `php artisan down --secret="[token]"` untuk membatasi akses publik.
2.  **Revoke**: Invalidate seluruh sesi aktif via Redis FLUSH atau `php artisan auth:clear-tokens`.
3.  **Hubungi Tim Keamanan**:
    *   **Emergency Contact**: security@birawa.id (24/7)
    *   **Hotline**: +62-800-SECURE-VET
4.  **Recovery**: Restore database dari backup bersih terakhir (Point-in-Time Recovery) jika data integritas terganggu.

---

## Glossary

*   **RBAC (Role-Based Access Control)**: Metode pembatasan akses sistem berdasarkan peran pengguna.
*   **MFA (Multi-Factor Authentication)**: Metode verifikasi identitas menggunakan dua atau lebih bukti (password + kode token).
*   **TOTP (Time-based One-Time Password)**: Algoritma yang menghasilkan kode unik sementara berdasarkan waktu saat ini.
*   **Immutable Log**: Catatan log yang tidak dapat diubah atau dihapus setelah dibuat, menjamin integritas audit.
*   **Sudo Mode**: Mode keamanan sementara yang meminta autentikasi ulang sebelum melakukan tindakan sensitif.
