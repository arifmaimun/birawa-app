# Birawa Vet - Sistem Manajemen Klinik Hewan

## Deskripsi

Aplikasi manajemen klinik hewan berbasis web yang fokus pada layanan Home Visit. Sistem terintegrasi dengan Point of Sale (POS), Inventory Management, dan Invoicing dalam platform terpadu.

## Tech Stack

- Laravel 11
- PHP 8.2+
- SQLite (Development)
- Tailwind CSS

## Setup

1. Clone repository
2. Install dependencies: `composer install`
3. Copy `.env.example` to `.env` and configure
4. Run migrations: `php artisan migrate`
5. Seed database: `php artisan db:seed`

## Database Schema

- **Users**: Manajemen pengguna dengan RBAC (superadmin, veterinarian)
- **Owners**: Data pemilik hewan
- **Patients**: Data pasien hewan
- **Products**: Inventory barang dan jasa
- **Visits**: Catatan kunjungan medis
- **Invoices**: Header faktur
- **Invoice Items**: Detail transaksi

## Roadmap

- [x] Setup Environment & Database Schema
- [x] Factory Classes & Seeding
- [ ] POS Module
- [ ] Inventory Management
- [ ] PDF Invoice Generation
- [ ] Dashboard & Reporting

## Kontribusi

Dikembangkan oleh Drh. Arif Maimun
