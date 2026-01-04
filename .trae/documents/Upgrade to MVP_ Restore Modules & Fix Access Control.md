I will upgrade the application to MVP status by restoring critical modules, fixing access control, and ensuring data privacy (scoping).

### 1. Restore Core Modules (Move from Archived)
The following resources are currently archived and inaccessible. I will move them back to `app/Filament/Resources`:
- **Clinical**: `PatientResource`, `MedicalRecordResource`, `VisitResource`.
- **Finance**: `InvoiceResource`.
- **Inventory**: `DoctorInventoryResource`, `ProductResource`.
- **CRM**: `ClientResource`.
- *Action*: Move files and directories from `app/Filament/ArchivedResources/` to `app/Filament/Resources/`.

### 2. Fix Access Control
Currently, only `superadmin` can access the `birawa-hub` panel.
- *Action*: Update `User::canAccessPanel` to allow users with role `veterinarian` (or `doctor`) to access the panel.

### 3. Implement Data Scoping (Privacy)
Ensure doctors only see their own data.
- *Action*: Add `modifyQueryUsing` to the `table()` method of the restored resources:
    - `ClientResource`: Scope by `user_id`.
    - `PatientResource`: Scope by `client.user_id`.
    - `VisitResource`: Scope by `user_id`.
    - `MedicalRecordResource`: Scope by `doctor_id`.
    - `InvoiceResource`: Scope by `user_id`.
    - `DoctorInventoryResource`: Scope by `user_id`.

### 4. Upgrade Invoice Module
The current `InvoiceResource` lacks item entry.
- *Action*: Add a `Repeater` to `InvoiceResource::form()` for `invoiceItems`.
    - Fields: `doctor_inventory_id` (Select product/service), `quantity`, `unit_price` (auto-filled/editable).

### 5. Refine Forms
- *Action*: Set `doctor_id` to `Auth::id()` and hide it in `MedicalRecordResource` and `VisitResource` to prevent tampering.

### 6. Verify Navigation
- *Action*: Organize resources into logical groups (`Clinical`, `Finance`, `Inventory`, `Master Data`) via `navigationGroup`.