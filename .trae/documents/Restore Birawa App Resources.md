# Rencana Detail: Standardisasi & Perbaikan Birawa Vet

Saya akan melaksanakan tugas ini dalam 3 fase terperinci untuk memastikan aplikasi memenuhi standar operasional dokter hewan mandiri.

## Fase 1: Implementasi Standar Operasional (Dokumentasi)
Saya akan membuat dokumen master `docs/HOME_VISIT_WORKFLOW.md` yang menerjemahkan visi Anda menjadi spesifikasi teknis.

**Struktur Dokumen yang Akan Dibuat:**
1.  **Identifikasi & Scope**
    - Definisi stakeholder (Dokter Mandiri, Klien).
    - Batasan layanan (Non-emergency home care).
2.  **Peta Tahapan (Step-by-Step)**
    - **Pra-Kunjungan:** SOP Verifikasi via WA, Input Booking di Aplikasi, Penentuan Jadwal.
    - **Persiapan:** SOP Cek Stok Tas Medis, Generate Rute via Aplikasi.
    - **Pelaksanaan:** SOP Start Trip (WA Blast), Navigasi, Protokol Tibanya Dokter, Pemeriksaan (SOAP), Tindakan.
    - **Pasca-Kunjungan:** SOP Invoice, Pembayaran, Jadwal Ulang (jika perlu).
3.  **Diagram Alur & Logika Bisnis**
    - *Decision Tree:* Jika Hewan Agresif -> Protokol Sedasi / Batal.
    - *Decision Tree:* Jika Owner Tidak Ada -> Protokol Reschedule / Charge Visit Fee.
4.  **Verifikasi & Checkpoint**
    - Daftar item wajib (Stetoskop, Termometer, Emergency Kit).

## Fase 2: Perbaikan Teknis (Enabling The App)
Agar aplikasi dapat diuji sesuai SOP di atas, saya akan melakukan perbaikan mendalam pada environment.

**Langkah Teknis Detail:**
1.  **Perbaikan Tampilan (Frontend Build)**
    - **Masalah:** Tombol navigasi dan layout berantakan karena Tailwind CSS belum terkompilasi.
    - **Tindakan:**
        - Jalankan `npm install` untuk mengunduh dependensi (Tailwind, Vite, Alpine.js).
        - Jalankan `npm run build` untuk menghasilkan file CSS/JS produksi di folder `public/build/`.
        - Verifikasi file `manifest.json` terbentuk.

2.  **Inisialisasi Data (Database Seeding)**
    - **Masalah:** Database kosong membuat fitur "Search Pasien" dan "Rute" tidak bisa dicoba.
    - **Tindakan:**
        - `php artisan migrate:fresh` (Reset total database).
        - `php artisan db:seed` (Jalankan seeder standar).
        - **Verifikasi Data:** Pastikan tercipta minimal:
            - 1 Akun Dokter (Login).
            - 5 Data Klien & Pasien (dengan koordinat lokasi berbeda).
            - 10 Item Inventory (Obat/Jasa).
            - 3 Jadwal Kunjungan hari ini (untuk tes Rute).

3.  **Konfigurasi Sistem**
    - `php artisan key:generate` (Security key).
    - `php artisan storage:link` (Agar foto pasien/profil bisa tampil).

## Fase 3: Verifikasi Fungsional (Gap Analysis)
Setelah aplikasi hidup, saya akan mensimulasikan **Skenario Normal** sesuai SOP Anda:

1.  **Simulasi Booking:** Login -> Input Pasien Baru -> Set Jadwal.
2.  **Simulasi Berangkat:** Buka Detail -> Klik "Start Trip" -> Cek apakah Link WA terbentuk benar.
3.  **Simulasi Medis:** Input SOAP -> Input Obat -> Cek apakah Stok berkurang.
4.  **Laporan:** Saya akan melaporkan fitur mana yang **Sudah Sesuai** dan mana yang **Perlu Ditambahkan** (misal: Aplikasi saat ini belum punya fitur "Checklist Tas Medis" digital).

Target Output: Aplikasi yang berjalan lancar dan dokumen SOP yang lengkap. Bisakah kita eksekusi sekarang?