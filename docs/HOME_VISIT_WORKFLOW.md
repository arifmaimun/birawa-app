# Standard Operating Procedure (SOP): Layanan Home Visit Dokter Hewan Mandiri

**Dokumen Referensi Utama**
*Versi 1.0 - 2026*

## 1. Identifikasi & Lingkup Layanan

### Tujuan
Memberikan layanan pemeriksaan, pengobatan, dan perawatan kesehatan hewan peliharaan secara profesional di lokasi pemilik (rumah), dengan standar keamanan dan medis yang setara dengan pemeriksaan dasar di klinik.

### Stakeholder
1.  **Dokter Hewan Mandiri**: Pelaksana utama medis dan operasional.
2.  **Klien (Pemilik Hewan)**: Penerima layanan.
3.  **Admin/Asisten (Opsional)**: Membantu penjadwalan (jika ada).

### Batasan Layanan (Scope)
*   **Layanan yang Diterima**: Vaksinasi rutin, pemeriksaan umum (sakit ringan), pengobatan kutu/jamur, vitamin/suplemen, luka ringan.
*   **Layanan yang DITOLAK (Wajib Rujuk)**: Operasi mayor, X-Ray/USG (kecuali portable tersedia), rawat inap intensif, kasus sesak nafas akut/kritis, hewan sangat agresif tanpa restrain.

---

## 2. Peta Tahapan (Process Map)

### Tahap A: Pra-Kunjungan (Booking & Verifikasi)
**Tujuan**: Memastikan kunjungan layak dilakukan dan jadwal terkunci.

1.  **Penerimaan Permintaan**
    *   *Input*: Klien menghubungi via WA/Telp atau Aplikasi.
    *   *Proses*: Dokter/Admin menanyakan:
        *   Jenis Hewan & Jumlah.
        *   Keluhan Utama / Tujuan (Vaksin/Sakit).
        *   Lokasi (Shareloc).
        *   Kondisi Hewan (Nafsu makan, Agresif/Tidak).
2.  **Verifikasi Kelayakan (Triage)**
    *   *Decision Point*: Apakah kasus darurat/kritis?
        *   **YA** -> Arahkan segera ke Klinik/RS Hewan terdekat (Tolak Home Visit).
        *   **TIDAK** -> Lanjut ke penjadwalan.
3.  **Penjadwalan & Input Data**
    *   Dokter membuka **Aplikasi Birawa Vet**.
    *   Menu: **Buat Jadwal (`/visits/create`)**.
    *   Cari/Input Data Klien Baru.
    *   Set Tanggal & Jam Estimasi.
    *   Input Catatan Awal (Anamnesa singkat).
4.  **Konfirmasi**
    *   Kirim konfirmasi booking ke Klien (Otomatis/Manual).

### Tahap B: Persiapan Kunjungan
**Tujuan**: Memastikan alat medis lengkap dan rute efisien.

1.  **Cek Jadwal Harian**
    *   Buka Menu **Jadwal (`/visits/calendar`)** di Aplikasi.
    *   Review daftar pasien hari ini.
2.  **Penyusunan Rute**
    *   Gunakan fitur **"Rekomendasi Rute"** di aplikasi untuk mengurutkan kunjungan berdasarkan lokasi terdekat.
3.  **Checklist Tas Medis (Physical Check)**
    *   *Alat Dasar*: Stetoskop, Termometer, Penlight, Timbangan Gantung/Portable.
    *   *Emergency Kit*: Epinefrin, Atropin, Cairan Infus & Set Infus (untuk pertolongan pertama).
    *   *Obat Jalan*: Antibiotik, Anti-radang, Vitamin, Obat Kutu (Sesuai inventory aplikasi).
    *   *Administrasi*: Form Consent (Persetujuan Tindakan), Nota/Invoice (jika manual).

### Tahap C: Pelaksanaan Kunjungan (On-Site)
**Tujuan**: Pemeriksaan medis dan penanganan.

1.  **Keberangkatan**
    *   Buka Detail Kunjungan di Aplikasi.
    *   Klik **"Mulai Berangkat"**.
    *   **Sistem**: Mengirim WA ke Klien *"Dokter OTW, estimasi X menit"*.
    *   Buka Navigasi (Google Maps) dari aplikasi.
2.  **Tiba di Lokasi**
    *   Klik **"Sampai di Lokasi"** di Aplikasi.
    *   **Protokol Masuk**:
        *   Salam & Konfirmasi Hewan.
        *   Pastikan hewan dalam keadaan terkendali (dipegang owner/kandang).
        *   Cuci tangan / Desinfeksi sebelum pegang hewan.
3.  **Pemeriksaan (Consultation)**
    *   Buka menu **"Start Consultation"** di Aplikasi.
    *   **Anamnesa**: Tanya ulang keluhan detail.
    *   **Pemeriksaan Fisik**: Cek suhu, berat badan, detak jantung, mukosa.
        *   *Input Data*: Masukkan ke kolom Vital Signs di Aplikasi.
4.  **Tindakan & Pengobatan**
    *   Jelaskan diagnosa sementara ke pemilik.
    *   Minta persetujuan tindakan (Informed Consent).
    *   Lakukan tindakan (Suntik/Infus/dll).
    *   Berikan obat jalan.
    *   *Input Data*: Masukkan Obat/Jasa yang dipakai ke Aplikasi (Stok berkurang otomatis).

### Tahap D: Pasca-Kunjungan & Pembayaran
**Tujuan**: Penyelesaian administrasi.

1.  **Finalisasi Layanan**
    *   Klik **"Simpan Rekam Medis"**.
    *   Sistem otomatis membuat **Invoice**.
2.  **Pembayaran**
    *   Tunjukkan total biaya (Jasa Medis + Obat + Transport Fee).
    *   Terima pembayaran (Tunai/Transfer/QRIS).
    *   Tandai Invoice sebagai **"Lunas/Paid"** di Aplikasi.
3.  **Follow Up**
    *   Jadwalkan kunjungan ulang jika perlu (Input jadwal baru).
    *   Pamit & Desinfeksi diri sebelum masuk mobil.

---

## 3. Protokol Penanganan Khusus (Exception Handling)

### Kasus A: Hewan Agresif
1.  Minta pemilik untuk memasang brangus (muzzle) atau memegang hewan (restrain).
2.  Jika pemilik tidak mampu mengendalikan -> **BATALKAN** pemeriksaan fisik demi keselamatan.
3.  Opsi: Beri obat penenang (jika memungkinkan) atau rujuk ke klinik untuk sedasi total.
4.  Update status kunjungan: **Cancelled** (Alasan: Agresif).

### Kasus B: Pemilik Tidak Ada di Lokasi (No Show)
1.  Tunggu maksimal 15 menit.
2.  Hubungi via Telp/WA 3x.
3.  Jika tidak ada respon -> Tinggalkan lokasi.
4.  Kirim pesan "Kunjungan Dibatalkan".
5.  Catat sebagai **"Missed Appointment"** (Blacklist jika berulang).

### Kasus C: Kondisi Hewan Memburuk Tiba-tiba (Emergency on Site)
1.  Lakukan pertolongan pertama (First Aid) untuk stabilisasi.
2.  Segera arahkan/antar ke Klinik/RS Hewan terdekat dengan fasilitas lengkap.
3.  Jangan memaksakan rawat jalan jika butuh oksigen/monitor intensif.

---

## 4. Verifikasi & KPI (Key Performance Indicators)

### Checkpoint Sukses
*   [ ] Tas medis lengkap sebelum berangkat.
*   [ ] Klien menerima notifikasi WA sebelum dokter tiba.
*   [ ] Rekam medis terisi lengkap (SOAP).
*   [ ] Pembayaran lunas di tempat.

### Kriteria Kegagalan (Rollback)
*   Jika alat ketinggalan -> Reschedule atau beli di apotek terdekat (jika bahan habis pakai).
*   Jika aplikasi down -> Gunakan Form Manual (Kertas), input ke sistem setelah sinyal stabil.

---

## 5. Dokumentasi Teknis (App Support)

### Fitur Pendukung di Aplikasi Birawa Vet
*   **Booking Engine**: Mencatat janji temu.
*   **Route Optimizer**: Efisiensi bensin dan waktu.
*   **Medical Record (EMR)**: Mencatat riwayat medis digital.
*   **Inventory Tracking**: Mencegah kehabisan obat saat visit.
*   **Auto-Invoice**: Transparansi harga ke klien.
