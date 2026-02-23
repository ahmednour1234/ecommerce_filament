# Service Transfer Module (نقل الخدمات / نقل كفالة)

This module manages service transfer requests for workers between customers.

## Installation

1. Run migrations:
```bash
php artisan migrate
```

2. Seed permissions and translations:
```bash
php artisan db:seed --class=Modules\\ServiceTransfer\\Database\\Seeders\\ServiceTransferPermissionsSeeder
php artisan db:seed --class=Modules\\ServiceTransfer\\Database\\Seeders\\ServiceTransferTranslationsSeeder
```

## Module Structure

- **Migrations**: Database schema for service transfers, payments, and documents
- **Entities**: Eloquent models with relations and business logic
- **Services**: Business logic and integration services
- **Filament Resources**: Admin UI for managing service transfers

## Features

- Service transfer request management with auto-generated request numbers (REQ-YYYYMMDD-####)
- Payment tracking with automatic status calculation
- Document management for transfers
- Status tracking: Transferred, Cancelled, In Trial, Multiple Trial, No Action Taken
- Trial period management with end date
- Full Arabic/English translations
- Permission-based access control

## Status Options

- **Transferred** (تم النقل): Service has been successfully transferred
- **Cancelled** (تم الإلغاء): Transfer request has been cancelled
- **In Trial** (في مرحلة تجربة): Currently in trial period
- **Multiple Trial** (عدة مرحلة تجربة): Multiple trial periods
- **No Action Taken** (ولم يتخذ إجراء): No action has been taken yet

## Permissions

- `service_transfer.view` - View service transfers
- `service_transfer.create` - Create service transfers
- `service_transfer.update` - Update service transfers
- `service_transfer.delete` - Delete service transfers
- `service_transfer.archive` - Archive service transfers
- `service_transfer.refund` - Refund service transfers

## Usage

1. Create service transfer requests with customer, worker, and package details
2. Track payments and update payment status automatically
3. Manage documents related to transfers
4. Track status and trial periods
5. Archive or refund transfers as needed
