# Rencana Penyempurnaan MVP (Fase 2)

Untuk memastikan aplikasi benar-benar "MVP" dan siap digunakan oleh Dokter Hewan, kita perlu menutup beberapa celah fungsionalitas operasional, terutama seputar **Tagihan (Invoice)** dan **Manajemen Stok**.

## 1. Peningkatan Aksesibilitas Invoice (Invoice Usability)
Saat ini, Invoice memiliki tampilan cetak yang bagus (`invoices.show`), tetapi sulit diakses dari Panel Admin Filament. Dokter harus masuk ke menu Edit yang membingungkan untuk menemukan link.

**Solusi:**
*   Menambahkan tombol **"Print / View"** langsung di tabel Invoice. Tombol ini akan membuka tampilan faktur yang siap cetak.
*   Menambahkan tombol **"Send via WhatsApp"** di tabel Invoice. Ini akan mengirimkan link faktur publik langsung ke klien via WA.

## 2. Widget Peringatan Stok Menipis (Low Stock Alert)
Kita sudah punya widget untuk "Batch Expired", tapi belum ada peringatan jika **jumlah stok** obat habis atau menipis. Ini krusial agar dokter tidak kehabisan obat saat kunjungan.

**Solusi:**
*   Membuat widget dashboard baru: `LowStockAlertWidget`.
*   Widget ini akan menampilkan daftar obat (`DoctorInventory`) yang `stock_qty` <= `alert_threshold`.
*   Hanya menampilkan stok milik dokter yang sedang login.

## 3. Verifikasi & Build
*   Memastikan semua link berfungsi.
*   Menjalankan build frontend untuk memastikan tampilan cetak Invoice (Tailwind CSS) termuat dengan benar.

Apakah Anda setuju dengan tambahan fitur ini?