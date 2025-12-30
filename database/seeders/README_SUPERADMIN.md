# Super Admin Seeder

This seeder creates and maintains the `super_admin` role with **ALL permissions** in the system.

## Usage

### Run as part of full database seeding:
```bash
php artisan db:seed
```

### Run standalone:
```bash
php artisan db:seed --class=SuperAdminSeeder
```

## What it does:

1. **Creates/Updates Role**: Creates the `super_admin` role if it doesn't exist
2. **Assigns ALL Permissions**: Automatically syncs ALL permissions from the database to the `super_admin` role
3. **Includes All Modules**: 
   - System permissions (users, roles, permissions)
   - MainCore permissions (currencies, branches, warehouses, etc.)
   - Catalog permissions (products, categories, brands, etc.)
   - Sales permissions (orders, invoices, customers, etc.)
   - Accounting permissions (journal entries, accounts, vouchers, reports, etc.)
   - Any future permissions added to the system

4. **Assigns to Users**: Automatically assigns the role to:
   - `admin@example.com` (if exists)
   - First user in database (if admin doesn't exist)

## Features:

- ✅ **Automatic**: Gets ALL permissions from database (no need to manually list)
- ✅ **Future-proof**: Automatically includes new permissions as they're added
- ✅ **Safe**: Can be run multiple times without issues
- ✅ **Informative**: Shows summary of permissions and users

## Manual Assignment

To assign `super_admin` role to a specific user:

```php
use App\Models\User;
use Spatie\Permission\Models\Role;

$user = User::find(1); // or User::where('email', 'user@example.com')->first();
$user->assignRole('super_admin');
```

## Verify

Check if a user has super_admin role:
```php
$user->hasRole('super_admin'); // returns true/false
```

Check role permissions:
```php
$role = Role::where('name', 'super_admin')->first();
$role->permissions()->count(); // total permissions
```

