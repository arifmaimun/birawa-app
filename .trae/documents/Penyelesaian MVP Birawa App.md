# Rencana Penyelesaian MVP (Minimum Viable Product)

Berdasarkan analisis, aplikasi ini sudah memiliki fitur inti yang baik, namun masih memerlukan beberapa penyesuaian UX dan alur kerja agar benar-benar siap digunakan sebagai MVP (Minimum Viable Product) yang layak, terutama untuk pengalaman pengguna Dokter/Klinik.

Berikut adalah rencana kerjanya:

## 1. Pengelompokan Navigasi (UX Improvement)
Saat ini menu navigasi mungkin berantakan. Saya akan mengelompokkan menu agar lebih rapi dan intuitif:
*   **Clinical:** `Client`, `Patient`, `Visit`, `MedicalRecord`
*   **Finance:** `Invoice`
*   **Inventory:** `DoctorInventory`, `Product`, `DoctorInventoryBatch`
*   **Management:** `User`, `DoctorProfile`

## 2. Otomatisasi Tagihan (Critical Workflow)
Saat ini, setelah Dokter mengisi Rekam Medis (Medical Record), mereka harus membuat Invoice secara manual dari nol. Ini tidak efisien.
*   **Implementasi:** Menambahkan tombol **"Create Invoice"** pada `MedicalRecordResource`.
*   **Fungsi:** Tombol ini akan otomatis:
    1.  Membuat Invoice baru untuk kunjungan tersebut.
    2.  Menyalin obat/barang yang digunakan di `MedicalRecord` (usageLogs) menjadi item tagihan (`InvoiceItem`).
    3.  Mengambil harga dari master produk.
    4.  Mengarahkan dokter langsung ke halaman Edit Invoice untuk review dan pembayaran.

## 3. Verifikasi Dashboard
Memastikan Widget Dashboard (`DailyProfitStats`, `ExpiringBatchesWidget`) muncul dengan benar untuk role `veterinarian` agar mereka bisa melihat performa harian mereka.

Langkah-langkah ini akan menutup celah antara "aplikasi yang berfungsi" dan "aplikasi yang siap pakai (MVP)".

Apakah Anda setuju dengan rencana ini?