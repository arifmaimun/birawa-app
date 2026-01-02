# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased] - 2026-01-01

### Fixed
- **Visits**:
    - Fixed issue where `/visits/create` only showed patients with previous history. Now allows searching and selecting any patient in the system.
    - Fixed `/visits` filter logic where searching by name broke the doctor-scoping rule (logic grouping fix).
    - Fixed `/visits` status filter which was previously ignored.
    - Added validation for visit filter parameters.

## [Unreleased] - 2025-12-29

### Added
- **Booking System**:
    - "Aksi Dokter" buttons (Mulai Berangkat, Sampai) in visit detail view.
    - Travel tracking (departure time, estimated duration, actual arrival time).
    - SOS feature with location sharing.
    - Route visualization using Google Maps iframe and navigation links.
    - Message templates integration for automated WhatsApp notifications (On Departure, On Arrival).
- **Inventory Management**:
    - `StockOpname` and `StockOpnameItem` models and migrations.
    - `InventoryTransfer` and `InventoryTransferItem` models for warehouse transfers.
    - `LowStockAlert` notification (mail and database channels).
    - Trigger `LowStockAlert` automatically when Stock Opname completion results in low stock.
    - `InventoryTransactionController` for viewing mutation history.
    - Expiry report view with batch tracking and color-coded status.
    - Multi-channel notification triggers (email/database) for low stock.
- **Product & Service Management**:
    - Product SKU auto-generation logic (`[Kategori]-[Tanggal]-[Sequence]`).
    - Real-time SKU uniqueness check.
    - Rich text editor (Trix) for service descriptions.
    - Duration-based service price calculator using Alpine.js.
    - Separation of Product (Barang) and Service (Jasa) forms and views.
- **Patient Management**:
    - Unified patient forms using reusable modal component.
    - AJAX-based edit form loading in modal.
    - Improved DOB input with masking, validation, and dual text/date picker support.
    - Removed top navigation bar from patient forms via `hideHeader` prop in `app-layout`.

### Changed
- **Calendar**:
    - Improved error handling with user-friendly error message and retry button.
    - Integrated loading spinner with FullCalendar events.
    - Wrapped `calendarEvents` controller method in try-catch block for robust error handling.
- **Visits**:
    - Replaced legacy `status` column access with `VisitStatus` relationship (`visitStatus->slug`).
    - Fixed duplicate `estimated_hours` input field.
    - Scoped SOS feature to 'on-the-way' status.
- **UI/UX**:
    - Enhanced `form-fields.blade.php` with better client search and validation.
    - Added `hideHeader` and `hideNav` props to `app-layout` for focused views.

### Fixed
- Fixed missing `@stack('styles')` in `app-layout.blade.php` causing Trix editor issues.
- Fixed linter errors in `services/edit.blade.php`.
- Fixed foreign key constraints in Stock Opname migrations.

### Security
- Added `Auth::id()` global scope/checks to ensure doctors only see their own data (visits, inventory, etc.).
