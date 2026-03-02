<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateModuleRoleSeeders extends Command
{
    protected $signature = 'modules:generate-role-seeders {--force : Overwrite existing seeders}';

    protected $description = 'Generate role seeders for all modules based on their permission seeders';

    protected $moduleRoleNames = [
        'CompanyVisas' => 'مدير تأشيرات الشركة',
        'Clients' => 'مدير العملاء',
        'Rental' => 'مدير قسم التأجير',
        'Recruitment' => 'مدير الاستقدام',
        'RecruitmentContract' => 'مدير عقود الاستقدام',
        'Messaging' => 'مدير قسم الرسائل',
        'Accounting' => 'مدير المحاسبة',
        'Complaint' => 'مدير الشكاوى',
        'EmployeeCommission' => 'مدير عمولات الموظفين',
        'Hr' => 'مدير الموارد البشرية',
        'Housing' => 'مدير قسم الإيواء',
        'DriverManagement' => 'مدير إدارة السائقين',
        'ServiceTransfer' => 'مدير نقل الخدمات',
        'Packages' => 'مدير باقات العروض',
        'Notifications' => 'مدير التنبيهات',
        'Finance' => 'مدير قسم الحسابات',
    ];

    public function handle(): int
    {
        $this->info('═══════════════════════════════════════════════════════');
        $this->info('Generating Role Seeders for All Modules');
        $this->info('═══════════════════════════════════════════════════════');
        $this->newLine();

        $permissionSeeders = $this->findPermissionSeeders();
        $created = 0;
        $skipped = 0;

        foreach ($permissionSeeders as $seederPath => $seederClass) {
            $moduleName = $this->extractModuleName($seederPath);
            
            if (!$moduleName) {
                continue;
            }

            $permissions = $this->extractPermissions($seederPath);
            
            if (empty($permissions)) {
                $this->warn("⚠ Could not extract permissions from: {$seederPath}");
                continue;
            }

            $roleSeederPath = $this->generateRoleSeeder($moduleName, $permissions, $seederPath);
            
            if ($roleSeederPath) {
                $created++;
                $this->info("✓ Created: {$roleSeederPath}");
            } else {
                $skipped++;
            }
        }

        $this->newLine();
        $this->info('═══════════════════════════════════════════════════════');
        $this->info("Created: {$created} seeders");
        $this->info("Skipped: {$skipped} seeders (already exist)");
        $this->info('═══════════════════════════════════════════════════════');

        return self::SUCCESS;
    }

    protected function findPermissionSeeders(): array
    {
        $seeders = [];
        
        $paths = [
            database_path('seeders'),
            base_path('Modules'),
        ];

        foreach ($paths as $basePath) {
            if (!File::exists($basePath)) {
                continue;
            }

            $files = File::allFiles($basePath);
            
            foreach ($files as $file) {
                $path = $file->getPathname();
                $filename = $file->getFilename();
                
                if (str_contains($filename, 'PermissionsSeeder.php') && 
                    !str_contains($filename, 'RoleSeeder')) {
                    
                    $relativePath = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $path);
                    $className = $this->getClassNameFromPath($path);
                    
                    if ($className) {
                        $seeders[$path] = $className;
                    }
                }
            }
        }

        return $seeders;
    }

    protected function getClassNameFromPath(string $path): ?string
    {
        $content = File::get($path);
        
        if (preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatch) &&
            preg_match('/class\s+(\w+)/', $content, $classMatch)) {
            return $namespaceMatch[1] . '\\' . $classMatch[1];
        }

        return null;
    }

    protected function extractModuleName(string $path): ?string
    {
        $pathParts = explode(DIRECTORY_SEPARATOR, $path);
        $filename = basename($path, '.php');
        
        $moduleName = str_replace('PermissionsSeeder', '', $filename);
        
        if (str_contains($path, 'Modules')) {
            foreach ($pathParts as $part) {
                if ($part === 'Modules' && isset($pathParts[array_search($part, $pathParts) + 1])) {
                    $moduleName = $pathParts[array_search($part, $pathParts) + 1];
                    break;
                }
            }
        }

        return $moduleName ?: null;
    }

    protected function extractPermissions(string $path): array
    {
        $content = File::get($path);
        $permissions = [];

        $arrayPatterns = [
            '/\$permissions\s*=\s*\[(.*?)\];/s',
            '/\$allPermissions\s*=\s*\[(.*?)\];/s',
            '/\$.*Permissions\s*=\s*\[(.*?)\];/s',
            '/Permission::whereIn\(["\']name["\'],\s*\[(.*?)\]\)/s',
        ];

        foreach ($arrayPatterns as $pattern) {
            if (preg_match_all($pattern, $content, $matches)) {
                foreach ($matches[1] as $arrayContent) {
                    if (preg_match_all('/["\']([a-z_]+(?:\.[a-z_]+)+)["\']/i', $arrayContent, $permMatches)) {
                        $permissions = array_merge($permissions, $permMatches[1]);
                    }
                }
            }
        }

        $directPatterns = [
            '/Permission::firstOrCreate\(\s*\[["\']name["\']\s*=>\s*["\']([^"\']+)["\']/',
            '/["\']name["\']\s*=>\s*["\']([a-z_]+(?:\.[a-z_]+)+)["\']/i',
        ];

        foreach ($directPatterns as $pattern) {
            if (preg_match_all($pattern, $content, $matches)) {
                foreach ($matches[1] as $match) {
                    if (str_contains($match, '.')) {
                        $permissions[] = $match;
                    }
                }
            }
        }

        $permissions = array_unique($permissions);
        $permissions = array_filter($permissions, function($perm) {
            return str_contains($perm, '.') && 
                   strlen($perm) > 3 && 
                   !str_contains($perm, 'guard_name') &&
                   !str_contains($perm, 'name');
        });

        return array_values($permissions);
    }

    protected function generateRoleSeeder(string $moduleName, array $permissions, string $permissionSeederPath): ?string
    {
        $roleName = $this->moduleRoleNames[$moduleName] ?? "مدير {$moduleName}";
        
        $pathParts = explode(DIRECTORY_SEPARATOR, $permissionSeederPath);
        $isModule = str_contains($permissionSeederPath, 'Modules');
        
        if ($isModule) {
            $moduleIndex = array_search('Modules', $pathParts);
            $moduleDir = $pathParts[$moduleIndex + 1] ?? $moduleName;
            $seederDir = base_path("Modules/{$moduleDir}/Database/Seeders");
            $namespace = "Modules\\{$moduleDir}\\Database\\Seeders";
            $className = "{$moduleName}RoleSeeder";
        } else {
            $seederIndex = array_search('seeders', $pathParts);
            $subDir = $pathParts[$seederIndex + 1] ?? '';
            
            if ($subDir && $subDir !== 'seeders') {
                $seederDir = database_path("seeders/{$subDir}");
                $namespace = "Database\\Seeders\\{$subDir}";
            } else {
                $seederDir = database_path('seeders');
                $namespace = "Database\\Seeders";
            }
            
            $className = "{$moduleName}RoleSeeder";
        }

        if (!File::exists($seederDir)) {
            File::makeDirectory($seederDir, 0755, true);
        }

        $seederPath = $seederDir . DIRECTORY_SEPARATOR . $className . '.php';

        if (File::exists($seederPath) && !$this->option('force')) {
            return null;
        }

        $permissionsString = $this->formatPermissions($permissions);

        $content = <<<PHP
<?php

namespace {$namespace};

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class {$className} extends Seeder
{
    public function run(): void
    {
        \$this->command->info('Creating {$roleName} role...');

        \$role = Role::firstOrCreate(['name' => '{$roleName}', 'guard_name' => 'web']);

        \$permissions = [
{$permissionsString}
        ];

        \$permissionModels = Permission::whereIn('name', \$permissions)->get();
        \$role->syncPermissions(\$permissionModels);

        \$this->command->info('✓ {$roleName} role created with ' . \$permissionModels->count() . ' permissions');

        \$superAdmin = Role::where('name', 'super_admin')->orWhere('name', 'Super Admin')->first();
        if (\$superAdmin) {
            \$superAdmin->givePermissionTo(\$permissionModels);
            \$this->command->info('✓ All {$moduleName} permissions assigned to Super Admin role');
        }
    }
}

PHP;

        File::put($seederPath, $content);

        return str_replace(base_path() . DIRECTORY_SEPARATOR, '', $seederPath);
    }

    protected function formatPermissions(array $permissions): string
    {
        $formatted = [];
        foreach ($permissions as $permission) {
            $formatted[] = "            '{$permission}',";
        }
        return implode("\n", $formatted);
    }
}
