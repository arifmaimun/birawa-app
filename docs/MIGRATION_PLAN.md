# Rencana Migrasi Filament ke Implementasi Manual

Dokumen ini merinci strategi komprehensif untuk menghapus ketergantungan pada Filament dan menggantinya dengan implementasi manual berbasis Laravel standard (MVC).

## 1. Katalog Fitur Filament Saat Ini

### Dependensi Utama
- `filament/filament`: Admin Panel Core
- `bezhansalleh/filament-shield`: Manajemen Role & Permission
- `shuvroroy/filament-spatie-laravel-backup`: UI Backup

### Modul & Resource
Berikut adalah daftar modul yang perlu dimigrasikan, dikelompokkan berdasarkan domain:

**A. Core & CRM**
- UserResource (Manajemen Pengguna)
- ClientResource (Data Klien/Pemilik Hewan)
- PatientResource (Data Hewan)
- VisitResource (Kunjungan & Antrian)
- VisitStatusResource (Status Kunjungan)

**B. Clinical (Rekam Medis)**
- MedicalRecordResource (Rekam Medis Utama)
- DiagnosisResource (Master Diagnosa)
- VitalSignResource (Tanda Vital)
- VitalSignSettingResource (Konfigurasi Vital Sign)
- ConsentTemplateResource (Template Persetujuan Tindakan)

**C. Inventory & Produk**
- ProductResource (Master Produk)
- DoctorInventoryResource (Stok Dokter)
- StockOpnameResource (Penyesuaian Stok)
- InventoryTransferResource (Transfer Stok)
- StorageLocationResource (Lokasi Penyimpanan)
- ExpiringBatchesWidget (Notifikasi Kadaluarsa)
- LowStockAlertWidget (Notifikasi Stok Menipis)

**D. Finance**
- InvoiceResource (Tagihan)
- ExpenseResource (Pengeluaran)
- PromoResource (Diskon/Promo)
- DailyProfitStats (Widget Keuntungan)
- CashierStats (Widget Kasir)

**E. Management & HR**
- DoctorProfileResource (Profil Dokter)
- DoctorShiftResource (Jadwal Jaga)
- AuditLogResource (Log Aktivitas)

## 2. Arsitektur Pengganti (Manual MVC)

Kami akan menggunakan arsitektur **Laravel MVC Standard** dengan stack teknologi berikut untuk menjaga kemudahan maintainability dan performa:

- **Routing**: `routes/manual.php` (Terpisah dari rute web utama/filament)
- **Controllers**: `App\Http\Controllers\Manual\*`
- **Views**: Blade Templates di `resources/views/manual/*`
- **Styling**: Tailwind CSS (Konsisten dengan styling yang ada)
- **Icons**: Heroicons (via Blade UI Kit atau SVG manual)
- **Forms & Validation**: Laravel Form Request Validation
- **Tables**: HTML Table standard dengan Pagination Laravel + Search Query Scope

### Struktur Direktori Baru

```
app/
├── Http/
│   ├── Controllers/
│   │   └── Manual/
│   │       ├── Auth/ (Login manual jika lepas dari Filament Auth)
│   │       ├── Clinical/
│   │       ├── Finance/
│   │       ├── Inventory/
│   │       └── DashboardController.php
│   └── Requests/
│       └── Manual/
resources/
├── views/
│   └── manual/
│       ├── layouts/
│       │   ├── app.blade.php (Main Layout)
│       │   └── navigation.blade.php
│       ├── components/ (Table, Form Input, Modal)
│       ├── clinical/
│       ├── inventory/
│       └── dashboard.blade.php
routes/
└── manual.php
```

## 3. Strategi Migrasi Bertahap

Untuk menghindari *downtime* dan risiko besar, kita akan menggunakan pendekatan **Parallel Run** dengan **Feature Flags**.

### Mekanisme Feature Flag
File konfigurasi `config/migration.php` akan mengontrol akses modul.

```php
// config/migration.php
return [
    'enabled' => true, // Master switch
    'modules' => [
        'inventory' => false, // Masih pakai Filament
        'finance' => false,
        'clinical' => false,
    ],
    'route_prefix' => 'app', // URL prefix untuk aplikasi manual (misal: /app/dashboard)
];
```

### Layer Kompatibilitas
1. **User & Auth**: Menggunakan tabel `users` yang sama. Filament dan Manual App akan berbagi sesi login (Guard `web`).
2. **Permissions**: Menggunakan `spatie/laravel-permission` secara langsung di Controller manual, menggantikan `Filament::registerPolicies`.

## 4. Rencana Implementasi

### Fase 1: Infrastruktur Dasar (Current Step)
- [x] Katalogisasi
- [ ] Setup Routing & Middleware
- [ ] Setup Base Layout (Sidebar, Navbar)
- [ ] Dashboard Dummy

### Fase 2: Migrasi Modul Prioritas Rendah (Inventory)
Modul inventory relatif independen dan CRUD-heavy, cocok untuk validasi arsitektur.
- ProductResource -> Manual ProductController
- DoctorInventoryResource -> Manual InventoryController

### Fase 3: Migrasi Core & Clinical
- Client & Patient Management
- Medical Record (Kompleks: butuh form dinamis/repeater pengganti Filament Repeater)

### Fase 4: Migrasi Finance & Reporting
- Invoice & Payment
- Reporting Widgets

### Fase 5: Cleanup
- Hapus rute Filament
- Uninstall package Filament
- Hapus file di `app/Filament`

## 5. Standar Kode Implementasi Manual

### Controller Pattern
```php
public function index(Request $request)
{
    $query = Product::query();
    
    if ($search = $request->input('search')) {
        $query->where('name', 'like', "%{$search}%");
    }

    $products = $query->paginate(10);
    
    return view('manual.inventory.products.index', compact('products'));
}
```

### View Pattern
Menggunakan Blade Component untuk konsistensi UI (meniru kenyamanan Filament Component).
```blade
<x-manual.layout>
    <x-manual.page-header title="Daftar Produk" />
    <x-manual.table :headers="['Nama', 'SKU', 'Harga']">
        @foreach($products as $product)
        <tr>
            <td>{{ $product->name }}</td>
            <!-- ... -->
        </tr>
        @endforeach
    </x-manual.table>
</x-manual.layout>
```

## 6. Pengujian
Setiap modul yang dimigrasikan harus melewati:
1. **Unit Test**: Test Controller & FormRequest.
2. **UI Test**: Pastikan tampilan responsif.
3. **Data Integrity**: Cek apakah data yang diinput di Manual App muncul di Filament (selama masa transisi).
