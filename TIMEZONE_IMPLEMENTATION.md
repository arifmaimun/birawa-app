# Dokumentasi Implementasi Pengaturan Time Zone dan Foto Profil

## 1. Pengaturan Time Zone

Sistem ini mendukung pengaturan time zone per dokter, yang memungkinkan tampilan waktu di seluruh aplikasi disesuaikan dengan lokasi pengguna.

### Daftar Time Zone yang Didukung
Sistem menggunakan database Time Zone IANA (Olson database) yang disediakan oleh PHP. Daftar ini mencakup seluruh time zone valid di dunia, seperti:
- `UTC`
- `Asia/Jakarta` (WIB)
- `Asia/Makassar` (WITA)
- `Asia/Jayapura` (WIT)
- `America/New_York`
- dll.

Untuk melihat daftar lengkap, Anda dapat menggunakan helper di code:
```php
app(App\Services\TimezoneService::class)->getTimezones();
```

### Implementasi Kode

#### Setting Time Zone
Time zone disimpan di tabel `doctor_profiles` kolom `timezone`.
Pengaturan dilakukan via halaman Edit Profil Dokter di panel admin.

#### Konversi Waktu
Kami menyediakan `App\Services\TimezoneService` untuk menangani konversi.

**Contoh Penggunaan:**

```php
use App\Services\TimezoneService;

$service = new TimezoneService();

// Konversi dari Local ke UTC (untuk penyimpanan/API)
$utcTime = $service->convertFromLocalToUtc('2023-10-10 10:00:00', 'Asia/Jakarta');
// Hasil: 2023-10-10 03:00:00 (Carbon instance)

// Konversi dari UTC ke Local (untuk display manual jika tidak otomatis)
$localTime = $service->convertFromUtcToLocal('2023-10-10 03:00:00', 'Asia/Jakarta');
// Hasil: 2023-10-10 10:00:00 (Carbon instance)
```

#### Middleware Otomatis & Filament Configuration
Middleware `App\Http\Middleware\SetUserTimezone` secara otomatis mengatur konfigurasi `app.display_timezone` berdasarkan preferensi dokter yang sedang login. 

**PENTING**: `config('app.timezone')` tetap dipertahankan sebagai `UTC` untuk menjamin integritas data di database.

Kami menggunakan `FilamentTimezoneServiceProvider` untuk mengonfigurasi komponen Filament secara global agar menggunakan `app.display_timezone` tersebut. Ini mencakup:
- `TextColumn` (Tabel)
- `TextEntry` (Infolist)
- `DateTimePicker` (Form)

Ini memastikan bahwa:
1. User melihat waktu dalam Time Zone lokal mereka.
2. User menginput waktu dalam Time Zone lokal mereka.
3. Filament mengonversi input tersebut ke UTC sebelum disimpan ke database.
4. Data di database selalu konsisten dalam UTC.

### Troubleshooting

#### Time zone tidak dikenali
- Pastikan string time zone sesuai format IANA (Case Sensitive, e.g., 'Asia/Jakarta').
- Cek log aplikasi (`storage/logs/laravel.log`) untuk error "Invalid timezone".

#### Masalah Konversi Waktu
- Pastikan input waktu memiliki format yang benar.
- Periksa log dengan keyword "Timezone conversion failed".

#### Inkonsistensi Tampilan Waktu
- Pastikan user sudah login dan memiliki profile dokter.
- Pastikan field `timezone` di profile dokter terisi.
- Jika masih salah, coba clear cache aplikasi: `php artisan cache:clear`.

## 2. Foto Profil Dokter (Cache Busting)

Untuk mengatasi masalah browser caching pada foto profil yang diupdate, sistem menggunakan teknik "Cache Busting" dengan query parameter timestamp.

### Implementasi
Setiap kali URL avatar diakses via `getFilamentAvatarUrl()`, sistem menambahkan `?t={timestamp}` di akhir URL. Timestamp ini diambil dari `updated_at` user.

**Contoh URL:**
`https://domain.com/storage/avatars/user123.jpg?t=1697000000`

### Force Refresh
Saat user mengupload foto baru, `updated_at` user diperbarui, sehingga URL berubah dan browser dipaksa mengunduh gambar baru.

### Logging
Setiap perubahan avatar dicatat di log aplikasi:
`[INFO] User avatar updated for user 123`
