Product Canvas: Birawa Vet (Fokus: Platform Kemitraan Tertutup)

Visi: Platform manajemen praktik mandiri untuk dokter hewan home visit (Invite-Only).

🚑 1. Modul Operasional Dokter (Self-Managed)

Fokus: Dokter mengatur jadwal dan logistiknya sendiri secara mandiri.

Fitur

Deskripsi & Value

Prioritas

Doctor-Managed Calendar

Dokter membuat, mengedit, dan mengatur slot waktu kunjungan sendiri (Bukan ditentukan Admin). Tampilan kalender harian/mingguan.

🔥 Critical

Manual Booking Entry

Dokter menginput data janji temu baru (No HP Client + Jadwal) secara manual saat ada request via telp/chat pribadi.

🔥 Critical

GPS Distance Calculator

Saat dokter input alamat klien, sistem tetap menghitung jarak otomatis untuk menentukan surcharge biaya transport di invoice.

🔥 Critical

Personal Visit Tracker

Dokter menandai status kunjungannya sendiri ("OTW", "Sampai", "Selesai") untuk catatan pribadi dan notifikasi ke klien.

High

🏥 2. Modul Medis Mobile

Fokus: Akses rekam medis yang aman & etis antar dokter.

Fitur

Deskripsi & Value

Prioritas

Mobile EMR (Split Input)

Input SOAP dengan kolom terpisah antara Tindakan/Treatment (Visible to Client) dan Resep/Obat (Internal Only).

🔥 Critical

Inventory Usage Selector

Di bawah form resep, terdapat List/Checklist Barang dari Tas Dokter. Dokter tinggal pilih item & masukkan qty (cth: [v] Spuit 3ml (1), [v] Biodin (2ml)). Stok otomatis berkurang saat EMR disimpan.

🔥 Critical

Offline Mode Capability

Input data medis tetap bisa dilakukan dan tersimpan lokal meski sinyal di rumah klien buruk (Sync otomatis saat online).

🔥 Critical

Digital Referral Letter (Auto-Secure)

Surat rujukan (Link/PDF) ke Klinik lain. Akses publik (Guest) berlaku 48 jam. Lewat dari itu, Client wajib login untuk melihat surat rujukan demi keamanan data.

High

Peer Access Request

Riwayat medis dari dokter lain TERKUNCI (Blur/Hidden) secara default. Dokter baru bisa klik tombol "Minta Akses" untuk melihat detailnya.

🔥 Critical

Approval Notification System

Dokter lama menerima notifikasi (WA/In-App) ada permintaan akses. Dokter lama bisa Approve (Buka data) atau Deny (Tolak).

🔥 Critical

Riwayat Vaksinasi & Reminder

Cek riwayat vaksin saat kunjungan untuk menawarkan vaksin booster sekalian di tempat.

High

💊 3. Modul Inventori (Personal Stock)

Fokus: Manajemen stok obat pribadi yang dibawa dokter.

Fitur

Deskripsi & Value

Prioritas

Personal Mobile Stock

Stok obat melekat pada akun Dokter masing-masing. Dokter mengatur stok masuk/keluar tasnya sendiri.

🔥 Critical

Direct Deduction Logic

Stok berkurang berdasarkan Item yang Dipilih di Selector EMR, BUKAN berdasarkan parsing teks resep. Ini memastikan akurasi 100% tanpa error typo.

🔥 Critical

Restock Alert

Notifikasi ke dokter jika stok obat pribadinya menipis.

High

🛒 4. Modul Transaksi (Direct Payment & Custom Menu)

Fokus: Pembayaran langsung ke Dokter Mitra dengan tarif fleksibel.

Fitur

Deskripsi & Value

Prioritas

Personalized Service Menu

Dokter mengatur sendiri Daftar Layanan & Harga di pengaturan profilnya (cth: Jasa Visit=50rb, Suntik Scabies=75rb). Saat membuat invoice, dokter tinggal pilih dari menu ini.

🔥 Critical

Web-Based Invoice (Auto-Secure)

Generasi URL invoice unik berdasarkan item jasa yang dipilih dokter + obat (opsional). Akses publik 48 jam, selebihnya wajib login.

🔥 Critical

Partner Bank Details

Invoice otomatis menampilkan Rekening Pribadi Dokter (sesuai settingan profil dokter), bukan rekening pusat.

🔥 Critical

Manual Payment Confirmation

Dokter memiliki kontrol penuh tombol "Mark as Paid" setelah mengecek mutasi rekeningnya sendiri.

🔥 Critical

📱 5. Modul Klien & Onboarding (Doctor-Led)

Fokus: Client didaftarkan oleh Dokter, mencegah duplikasi data.

Fitur

Deskripsi & Value

Prioritas

Smart Deduplication (Cek No HP)

Saat dokter input No HP baru, sistem cek database. Jika Sudah Ada, dokter langsung "Link" ke akun tersebut tanpa perlu registrasi ulang.

🔥 Critical

Shared Pet Ownership (Co-Owner)

Mendukung relasi Many-to-Many: Satu hewan bisa terhubung ke akun Suami & Istri sekaligus. Keduanya bisa melihat update medis yang sama.

High

Doctor-Initiated Onboarding

Alur pendaftaran: Dokter input No HP -> Sistem buat Akun Sementara -> Dokter kirim Link Aktivasi via WA.

🔥 Critical

Smart WA Link Generator

Tombol wa.me yang otomatis berisi teks: "Halo, silakan lengkapi data hewan kakak & buat password akun di link ini: {onboard_link}".

🔥 Critical

Portal Client (Single ID)

Satu akun Client bisa terhubung ke banyak dokter, namun Client hanya perlu satu login untuk melihat riwayat dari semua dokter.

🔥 Critical

Safe Medical View (Client Mode)

Client bisa lihat diagnosa & tindakan, tapi TIDAK BISA lihat detail nama obat.

🔥 Critical

⚙️ 6. Manajemen Admin (Private Access)

Fokus: Manajemen User (Dokter) dan Kepatuhan.

Fitur

Deskripsi & Value

Prioritas

Private Partner Invite

Tidak ada halaman "Sign Up" untuk Dokter. Akun dokter dibuatkan manual oleh Admin (Invite Only).

🔥 Critical

Strictly Scoped Medical Access

Isolasi data ketat. Riwayat medis bersifat eksklusif milik pembuatnya, kecuali diberikan izin via fitur Peer Access Request.

🔥 Critical

Admin Override

Admin bisa mereset password Dokter/Client jika diminta bantuan.

High

Platform Monitoring

Admin memantau aktivitas transaksi global (sekadar untuk audit/royalti jika ada), tanpa campur tangan operasional harian.

Medium

Catatan Teknis (Alur Baru)

Logika Pengurangan Stok (Inventory Selector):

Di Database, relasi stok bukan ke medical_records.recipe_text, tapi ke tabel baru medical_usage_logs.

Saat dokter simpan EMR:

Simpan teks SOAP (untuk arsip bacaan).

Loop array selected_inventory_items.

Kurangi stok di doctor_inventory sesuai ID dan Qty.

Catat penggunaan di medical_usage_logs.

Logika Custom POS & Invoice:

Tabel doctor_service_catalog: Kolom doctor_id, service_name, price.

Fitur Setting: Dokter bisa CRUD (Create, Read, Update, Delete) daftar tarif mereka sendiri.

Invoice Builder: Dropdown "Tambah Item" di halaman invoice hanya menampilkan item dari doctor_service_catalog milik dokter yang sedang login + item Obat (jika ingin ditagihkan terpisah).

Relasi Database (Many-to-Many Pet Ownership):

Tabel pets: Menyimpan data hewan (Nama, Ras, Tgl Lahir).

Tabel users: Menyimpan data client (No HP, Password).

Tabel Pivot pet_owners: Penghubung yang berisi pet_id, user_id, dan is_primary (Boolean).

Logika "Peer Access Request" (Skenario Multi-Dokter):

Tampilan List: Riwayat diurutkan berdasarkan waktu (terbaru paling atas). Semua riwayat dari dokter lain (A, B, C) ditampilkan seragam: 🔒 Riwayat Medis Terkunci (Tanpa Tanggal, Tanpa Nama Dokter).

Routing Permintaan: Jika Dokter Baru klik gembok baris ke-1 (milik Dokter A), notif kirim ke Dokter A.

Logika Invoice (Akses & Rekening):

Rekening: Menggunakan data bank_account dari tabel doctors (bukan global settings).

Public Access (Guest): Jika invoice_created_at < 48 jam, halaman invoice bisa dibuka siapa saja yang punya link.

Secure Access (Login Required): Jika invoice_created_at > 48 jam, pengunjung di-redirect ke halaman login.