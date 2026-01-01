# Translation System Guide

This guide explains how to use the database-driven translation system in the Filament admin panel.

## Overview

The application uses a database-driven translation system where all translations are stored in the `translations` table. Translations are organized by groups (e.g., 'dashboard', 'menu') and support multiple languages (English and Arabic).

## Translation Helper Functions

### `tr()` - Main Translation Helper

The primary translation helper function with group support:

```php
tr(string $key, array $replace = [], ?string $locale = null, string $group = 'dashboard'): string
```

**Usage Examples:**

```php
// Basic usage (uses 'dashboard' group by default)
tr('sidebar.accounting')

// With replacements
tr('messages.welcome', ['name' => 'John'], null, 'dashboard')

// With specific locale
tr('sidebar.reports', [], 'ar', 'dashboard')

// With custom group
tr('menu.dashboard', [], null, 'menu')
```

**Backward Compatibility:**

The old usage `tr($key, $default)` still works for backward compatibility and uses the 'menu' group:

```php
tr('menu.dashboard', 'Dashboard') // Old usage - uses 'menu' group
```

### `trans_dash()` - Dashboard Translations

A convenience helper specifically for dashboard group translations:

```php
trans_dash(string $key, ?string $default = null, array|string|null $replace = null, ?string $languageCode = null): string
```

**Usage:**

```php
trans_dash('reports.filters.branch', 'Branch')
```

## Translation Key Conventions

Follow these naming conventions for consistency:

- **Sidebar Navigation:**
  - Groups: `sidebar.{group}` (e.g., `sidebar.accounting`, `sidebar.reports`)
  - Items: `sidebar.{group}.{item}` (e.g., `sidebar.accounting.accounts_tree`)

- **Pages:**
  - Titles: `pages.{section}.{page}.title` (e.g., `pages.reports.changes_in_equity.title`)
  - Headings: `pages.{section}.{page}.heading`

- **Tables:**
  - Columns: `tables.{resource}.{column}` (e.g., `tables.accounts.code`)
  - Filters: `tables.{resource}.filters.{filter}`
  - Empty states: `tables.{resource}.empty_state.heading`

- **Forms:**
  - Labels: `forms.{resource}.{field}.label`
  - Placeholders: `forms.{resource}.{field}.placeholder`
  - Helper texts: `forms.{resource}.{field}.helper`

- **Actions:**
  - Buttons: `actions.{action}` (e.g., `actions.create`, `actions.export_excel`)

- **Common Terms:**
  - General: `common.{term}` (e.g., `common.select`, `common.yes`, `common.no`)

- **Reports:**
  - Specific: `reports.{report}.{element}` (e.g., `reports.trial_balance.account_code`)

## Using Translations in Filament

### In Resources

```php
use App\Filament\Concerns\TranslatableNavigation;

class AccountResource extends Resource
{
    use TranslatableNavigation;
    
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?string $navigationTranslationKey = 'sidebar.accounting.accounts_tree';
    
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('code')
                ->label(tr('forms.accounts.code.label', [], null, 'dashboard'))
                ->placeholder(tr('forms.placeholders.code', [], null, 'dashboard')),
        ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('code')
                ->label(tr('tables.accounts.code', [], null, 'dashboard')),
        ]);
    }
}
```

### In Pages

```php
class ChangesInEquityReportPage extends Page
{
    protected static ?string $navigationGroup = 'Reports';
    
    public static function getNavigationGroup(): ?string
    {
        return tr('sidebar.reports', [], null, 'dashboard');
    }
    
    public static function getNavigationLabel(): string
    {
        return tr('sidebar.reports.changes_in_equity', [], null, 'dashboard');
    }
    
    public static function getTitle(): string
    {
        return tr('pages.reports.changes_in_equity.title', [], null, 'dashboard');
    }
    
    public function getHeading(): string
    {
        return tr('pages.reports.changes_in_equity.title', [], null, 'dashboard');
    }
}
```

### In Forms

```php
Forms\Components\TextInput::make('name')
    ->label(tr('forms.common.name', [], null, 'dashboard'))
    ->placeholder(tr('forms.placeholders.name', [], null, 'dashboard'))
    ->helperText(tr('forms.helpers.required_field', [], null, 'dashboard'))
```

### In Tables

```php
Tables\Columns\TextColumn::make('name')
    ->label(tr('tables.common.name', [], null, 'dashboard'))
    ->searchable()
```

### In Actions

```php
Tables\Actions\EditAction::make()
    ->label(tr('actions.edit', [], null, 'dashboard'))

Tables\Actions\DeleteAction::make()
    ->label(tr('actions.delete', [], null, 'dashboard'))
```

### In Blade Views

```blade
{{ tr('common.welcome', [], null, 'dashboard') }}

<div>
    {{ tr('pages.reports.title', [], null, 'dashboard') }}
</div>
```

## Commands

### Audit Translations

Scan the codebase for translation keys and identify missing translations:

```bash
php artisan translations:audit
```

**Options:**

- `--insert-missing`: Automatically insert missing keys into database
- `--group=`: Filter by translation group
- `--format=`: Output format (table, json, csv)

**Examples:**

```bash
# Basic audit
php artisan translations:audit

# Insert missing keys automatically
php artisan translations:audit --insert-missing

# Filter by group
php artisan translations:audit --group=dashboard

# JSON output
php artisan translations:audit --format=json
```

### Seed Common Translations

Seed common translation keys (sidebar, actions, common terms, forms, tables):

```bash
php artisan translations:seed-menu-and-common
```

This command runs the following seeders:
- `SidebarTranslationsSeeder`
- `ActionsTranslationsSeeder`
- `CommonTermsSeeder`
- `FormsTranslationsSeeder`
- `TablesTranslationsSeeder`

### Seed Specific Translations

You can also run individual seeders:

```bash
php artisan db:seed --class=Database\\Seeders\\MainCore\\SidebarTranslationsSeeder
php artisan db:seed --class=Database\\Seeders\\MainCore\\ActionsTranslationsSeeder
php artisan db:seed --class=Database\\Seeders\\Reports\\ReportsTranslationsSeeder
```

## Adding New Translations

### Method 1: Using Seeders (Recommended)

1. Identify the appropriate seeder file (or create a new one)
2. Add your translations to the `$translations` array:

```php
$translations = [
    'your.new.key' => ['en' => 'English Text', 'ar' => 'النص العربي'],
];
```

3. Run the seeder:

```bash
php artisan db:seed --class=YourSeeder
```

### Method 2: Using the Admin Panel

1. Navigate to Settings > Translations
2. Click "Create Translation"
3. Fill in:
   - Key: `your.new.key`
   - Group: `dashboard` (or appropriate group)
   - Language: Select English or Arabic
   - Value: The translated text

### Method 3: Using the Audit Command

1. Use translation keys in your code: `tr('your.new.key', [], null, 'dashboard')`
2. Run the audit command with `--insert-missing`:

```bash
php artisan translations:audit --insert-missing
```

This will automatically insert missing keys (with the key as placeholder value).

## Translation Groups

- **dashboard**: Default group for most UI translations
- **menu**: Legacy group for menu items (backward compatibility)

## Fallback Chain

The translation system uses the following fallback chain:

1. Current locale translation
2. English translation (if current locale is not English)
3. Translation key itself (as last resort)

## Caching

Translations are cached for 1 hour (3600 seconds) to improve performance. Cache is automatically cleared when:
- Language is changed via `set_locale()`
- TranslationService `clearCache()` is called

To manually clear translation cache:

```php
app(\App\Services\MainCore\TranslationService::class)->clearCache();
```

Or clear all cache:

```bash
php artisan cache:clear
```

## Best Practices

1. **Always use translation keys** - Never hardcode English or Arabic text in UI
2. **Follow naming conventions** - Use consistent key structures
3. **Use appropriate groups** - Group related translations together
4. **Provide fallbacks** - Always provide English translations as fallback
5. **Run audit regularly** - Use `translations:audit` to find missing translations
6. **Test both languages** - Verify translations work in both English and Arabic
7. **Use seeders** - Prefer seeders over manual database entries for consistency

## Troubleshooting

### Translation not showing

1. Check if the key exists in the database:
   ```sql
   SELECT * FROM translations WHERE `key` = 'your.key';
   ```

2. Check if the language is active:
   ```sql
   SELECT * FROM languages WHERE code = 'en' OR code = 'ar';
   ```

3. Clear cache:
   ```bash
   php artisan cache:clear
   ```

### Missing translations

1. Run the audit command to find missing keys:
   ```bash
   php artisan translations:audit
   ```

2. Seed common translations:
   ```bash
   php artisan translations:seed-menu-and-common
   ```

3. Add missing translations manually or via seeders

## Examples

### Complete Resource Example

```php
<?php

namespace App\Filament\Resources\Accounting;

use App\Filament\Concerns\TranslatableNavigation;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;

class AccountResource extends Resource
{
    use TranslatableNavigation;
    
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?string $navigationTranslationKey = 'sidebar.accounting.accounts';
    
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('code')
                ->label(tr('forms.accounts.code.label', [], null, 'dashboard'))
                ->placeholder(tr('forms.placeholders.code', [], null, 'dashboard'))
                ->required()
                ->helperText(tr('forms.helpers.unique_code', [], null, 'dashboard')),
                
            Forms\Components\TextInput::make('name')
                ->label(tr('forms.accounts.name.label', [], null, 'dashboard'))
                ->required(),
        ]);
    }
    
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(tr('tables.accounts.code', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('name')
                    ->label(tr('tables.accounts.name', [], null, 'dashboard'))
                    ->searchable()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(tr('actions.edit', [], null, 'dashboard')),
                Tables\Actions\DeleteAction::make()
                    ->label(tr('actions.delete', [], null, 'dashboard')),
            ])
            ->emptyStateHeading(tr('tables.accounts.empty_state.heading', [], null, 'dashboard'))
            ->emptyStateDescription(tr('tables.accounts.empty_state.description', [], null, 'dashboard'));
    }
}
```

## Support

For issues or questions about the translation system, please refer to the codebase or contact the development team.

