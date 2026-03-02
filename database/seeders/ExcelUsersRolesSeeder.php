<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\PermissionGrouper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ExcelUsersRolesSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ ضع الملف هنا داخل مشروع Laravel:
        // storage/app/seeders/الايملات 88.xlsx
        $filePath = storage_path('app/seeders/الايملات 88.xlsx');

        // لو شغال على نفس بيئة الـ sandbox (اختياري):
        if (! file_exists($filePath) && file_exists('/mnt/data/الايملات 88.xlsx')) {
            @mkdir(dirname($filePath), 0775, true);
            copy('/mnt/data/الايملات 88.xlsx', $filePath);
        }

        if (! file_exists($filePath)) {
            $this->command?->error("Excel file not found: {$filePath}");
            return;
        }

        // 1) Load Excel
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true); // A,B,C...

        // 2) Find header row dynamically
        $headerRowIndex = null;
        $headerCols = [
            'email' => null,
            'password' => null,
            'job_title' => null,
            'permissions_text' => null,
        ];

        foreach ($rows as $i => $row) {
            $normalized = array_map(fn ($v) => $this->norm((string) $v), $row);

            // نبحث عن صف فيه "اسم المستخدم" و "الباسوورد" و "المسمي الوظيفي" و "الصلاحيات"
            $emailCol = $this->findCol($normalized, 'اسم المستخدم');
            $passCol  = $this->findCol($normalized, 'الباسوورد');
            $jobCol   = $this->findCol($normalized, 'المسمي الوظيفي');
            $permCol  = $this->findCol($normalized, 'الصلاحيات');

            if ($emailCol && $passCol && $jobCol && $permCol) {
                $headerRowIndex = $i;
                $headerCols['email'] = $emailCol;
                $headerCols['password'] = $passCol;
                $headerCols['job_title'] = $jobCol;
                $headerCols['permissions_text'] = $permCol;
                break;
            }
        }

        if (! $headerRowIndex) {
            $this->command?->error("Header row not found. Expected headers: اسم المستخدم, الباسوورد, المسمي الوظيفي, الصلاحيات");
            return;
        }

        // 3) Department/Module -> Role mapping (كما طلبت)
        $departmentToRoleMap = [
            'الموارد البشرية' => 'مدير الموارد البشرية',
            'المحاسبة' => 'مدير المحاسبة',
            'الحسابات' => 'مدير قسم الحسابات',
            'الاستقدام' => 'مدير الاستقدام',
            'عقود الاستقدام' => 'مدير عقود الاستقدام',
            'التأجير' => 'مدير قسم التأجير',
            'الرسائل' => 'مدير قسم الرسائل',
            'الشكاوى' => 'مدير الشكاوى',
            'عمولات الموظفين' => 'مدير عمولات الموظفين',
            'الإيواء' => 'مدير قسم الإيواء',
            'إدارة السائقين' => 'مدير إدارة السائقين',
            'نقل الخدمات' => 'مدير نقل الخدمات',
            'باقات العروض' => 'مدير باقات العروض',
            'التنبيهات' => 'مدير التنبيهات',
            'العملاء' => 'مدير العملاء',
            'تأشيرات الشركة' => 'مدير تأشيرات الشركة',
        ];

        // 4) Arabic module keywords -> module_key used by PermissionGrouper
        $arabicModuleToKey = [
            'لوحة التحكم' => 'dashboard',
            'الموارد البشرية' => 'hr',
            'المحاسبة' => 'accounting',
            'الحسابات' => 'accounting',
            'عقود الاستقدام' => 'recruitment',
            'الاستقدام' => 'recruitment',
            'الإيواء' => 'housing',
            'التأجير' => 'rental',
            'الرسائل' => 'messaging',
            'الشكاوي' => 'complaints',
            'الشكاوى' => 'complaints',
            'نقل الخدمات' => 'service_transfer',
            'نقل الكفاله' => 'service_transfer',
            'إدارة السائقين' => 'driver_management',
            'إدارة نقل السائقين' => 'driver_management',
            'باقات العروض' => 'packages',
            'العملاء' => 'clients',
            'تأشيرات الشركة' => 'company_visas',
            'التنبيهات' => 'notifications',
            'الإعدادات' => 'settings',
        ];

        $createdUsers = 0;
        $updatedUsers = 0;
        $createdRoles = 0;
        $warnings = 0;

        // 5) Iterate rows after header
        foreach ($rows as $i => $row) {
            if ($i <= $headerRowIndex) {
                continue;
            }

            $email = trim((string) ($row[$headerCols['email']] ?? ''));
            $plainPassword = (string) ($row[$headerCols['password']] ?? '');
            $jobTitle = trim((string) ($row[$headerCols['job_title']] ?? ''));
            $permText = trim((string) ($row[$headerCols['permissions_text']] ?? ''));

            if ($email === '') {
                continue; // skip empty
            }

            $cleanJobTitle = $this->cleanJobTitle($jobTitle);
            $isCeo = $this->looksLikeCeo($cleanJobTitle, $permText);

            // name fallback
            $name = $cleanJobTitle ?: Str::before($email, '@');
            if ($name === '') {
                $name = $email;
            }

            // default password fallback if empty
            $plainPassword = trim($plainPassword);
            if ($plainPassword === '') {
                $plainPassword = 'password';
            }

            // 3) Create/Update user
            $existsBefore = User::where('email', $email)->exists();

            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make($plainPassword),
                ]
            );

            $existsBefore ? $updatedUsers++ : $createdUsers++;

            // 4) Determine modules from text
            $modulesFound = $isCeo ? array_values(array_unique($arabicModuleToKey)) : $this->extractModules($permText, $arabicModuleToKey);

            // 4.1) Determine role name
            // - لو CEO: role ثابت
            // - غير كده: حاول تطلع role من department map باستخدام أول قسم مطابق في النص
            $roleName = $isCeo ? 'CEO' : $this->resolveRoleName($cleanJobTitle, $permText, $departmentToRoleMap);

            /** @var Role $role */
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);

            if ($role->wasRecentlyCreated) {
                $createdRoles++;
            }

            // 4.2) Sync role permissions
            if ($isCeo) {
                // لو عندك طريقة تجيب "كل الصلاحيات" من PermissionGrouper:
                $allPermissionNames = [];
                foreach (array_unique(array_values($arabicModuleToKey)) as $moduleKey) {
                    $modulePerms = PermissionGrouper::getModulePermissions($moduleKey);
                    $permissionNames = array_column($modulePerms, 'name');
                    $allPermissionNames = array_merge($allPermissionNames, $permissionNames);
                }
                $allPermissionNames = array_values(array_unique($allPermissionNames));
                $permissionModels = Permission::whereIn('name', $allPermissionNames)->get();
                $role->syncPermissions($permissionModels);
            } else {
                if (empty($modulesFound)) {
                    $warnings++;
                    $this->command?->warn("Row {$i}: Cannot detect modules for {$email}. perm_text={$permText}");
                }

                $rolePermissionNames = [];
                foreach ($modulesFound as $moduleKey) {
                    $modulePerms = PermissionGrouper::getModulePermissions($moduleKey);
                    if (empty($modulePerms)) {
                        $warnings++;
                        $this->command?->warn("Row {$i}: PermissionGrouper returned empty for module={$moduleKey} (email={$email})");
                        continue;
                    }
                    $permissionNames = array_column($modulePerms, 'name');
                    $rolePermissionNames = array_merge($rolePermissionNames, $permissionNames);
                }

                $rolePermissionNames = array_values(array_unique($rolePermissionNames));
                $permissionModels = Permission::whereIn('name', $rolePermissionNames)->get();
                $role->syncPermissions($permissionModels);
            }

            // 4.3) Assign role to user (single role)
            $user->syncRoles([$roleName]);
        }

        $this->command?->info("✅ ExcelUsersRolesSeeder finished.");
        $this->command?->info("Users created: {$createdUsers}, updated: {$updatedUsers}");
        $this->command?->info("Roles created: {$createdRoles}");
        $this->command?->info("Warnings: {$warnings}");
    }

    private function norm(string $text): string
    {
        $t = trim($text);
        $t = str_replace(["\u{200F}", "\u{200E}", "\t", "\n", "\r"], ' ', $t);
        $t = preg_replace('/\s+/', ' ', $t);
        return $t;
    }

    private function findCol(array $row, string $needle): ?string
    {
        foreach ($row as $col => $value) {
            if ($value === null) continue;
            if (Str::contains($value, $needle)) {
                return $col;
            }
        }
        return null;
    }

    private function cleanJobTitle(string $jobTitle): string
    {
        $t = $this->norm($jobTitle);
        // remove bullets and weird prefixes مثل: "•"
        $t = str_replace(['•', '-', '–', '—'], '', $t);
        $t = trim($t);
        return $t;
    }

    private function looksLikeCeo(string $jobTitle, string $permText): bool
    {
        $t1 = $this->norm($jobTitle);
        $t2 = $this->norm($permText);

        return Str::contains($t1, 'رئيس مجلس') ||
               Str::contains($t2, 'كل شيء') ||
               Str::contains($t2, 'الاطلاع علي كل شيء') ||
               Str::contains($t2, 'الاطلاع على كل شيء');
    }

    private function extractModules(string $permText, array $arabicModuleToKey): array
    {
        $text = $this->norm($permText);

        // split by common separators
        $parts = preg_split('/[،,\-\+\|\/]+/u', $text) ?: [];
        $parts = array_map(fn($p) => trim($p), $parts);

        $found = [];

        // direct contains search on full text too
        foreach ($arabicModuleToKey as $ar => $key) {
            if (Str::contains($text, $ar)) {
                $found[] = $key;
            }
        }

        // also scan parts
        foreach ($parts as $p) {
            foreach ($arabicModuleToKey as $ar => $key) {
                if ($p !== '' && Str::contains($p, $ar)) {
                    $found[] = $key;
                }
            }
        }

        return array_values(array_unique($found));
    }

    private function resolveRoleName(string $jobTitle, string $permText, array $departmentToRoleMap): string
    {
        // لو عنده مسمى وظيفي واضح، استخدمه كـ Role name (أفضل مطابق للواقع)
        if ($jobTitle !== '') {
            return $jobTitle;
        }

        // fallback: حاول تطلع role من القسم المذكور في الصلاحيات
        $text = $this->norm($permText);

        foreach ($departmentToRoleMap as $deptAr => $roleName) {
            if (Str::contains($text, $deptAr)) {
                return $roleName;
            }
        }

        return 'موظف';
    }
}
