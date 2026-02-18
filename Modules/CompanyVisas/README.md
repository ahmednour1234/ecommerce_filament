# Company Visas Module

This module manages company visa requests and recruitment contracts.

## Installation

1. Run migrations:
```bash
php artisan migrate
```

2. Seed permissions and translations:
```bash
php artisan db:seed --class=Modules\\CompanyVisas\\Database\\Seeders\\CompanyVisasPermissionsSeeder
php artisan db:seed --class=Modules\\CompanyVisas\\Database\\Seeders\\CompanyVisasTranslationsSeeder
```

3. (Optional) Seed demo data:
```bash
php artisan db:seed --class=Modules\\CompanyVisas\\Database\\Seeders\\CompanyVisasDemoSeeder
```

## Module Structure

- **Migrations**: Database schema for visa requests, contracts, workers, expenses, costs, and documents
- **Models**: Eloquent models with relations and business logic
- **Services**: Finance integration, code generation, and worker linking
- **Filament Resources**: Admin UI for managing visa requests and contracts

## Features

- Visa request management with auto-generated codes (VISA-YYYYMMDD-####)
- Contract management with auto-generated contract numbers (CON-YYYY-####)
- Worker linking to contracts with automatic count updates
- Expense tracking with finance integration
- Contract cost management with automatic journal entries
- Document management for contracts
- Full Arabic/English translations
- Permission-based access control

## Finance Integration

The module automatically creates journal entries for:
- Contract costs: Debit buyer account, Credit agent account
- Expenses: Debit expense account, Credit cash/bank account

Configure account mappings in `config/company_visas.php` (create if needed).

## Permissions

- `company_visas.requests.*` - Visa request permissions
- `company_visas.contracts.*` - Contract permissions
- `company_visas.link_workers` - Link workers to contracts
- `company_visas.add_expense` - Add expenses
- `company_visas.update_status` - Update contract status
- `company_visas.manage_cost` - Manage contract costs
- `company_visas.manage_documents` - Manage documents

## Usage

1. Create visa requests with profession, nationality, gender, and worker count
2. Create contracts linked to visa requests (optional)
3. Link workers to contracts (updates counts automatically)
4. Add expenses and costs (creates finance entries)
5. Upload and manage documents
