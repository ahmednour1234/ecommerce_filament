<?php

namespace Database\Seeders\HR;

use App\Models\HR\Department;
use App\Models\HR\Position;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PositionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding HR positions...');

        $positions = [
            // مجلس الإدارة - الإدارة التنفيذية - المدير العام
            [
                'title' => 'المدير العام',
                'department_slug' => 'مجلس-الإدارة-الإدارة-التنفيذية-المدير-العام',
                'description' => 'المسؤول عن إدارة الشركة بشكل عام',
                'active' => true,
            ],
            [
                'title' => 'نائب المدير العام',
                'department_slug' => 'مجلس-الإدارة-الإدارة-التنفيذية-المدير-العام',
                'description' => 'يساعد المدير العام في إدارة الشركة',
                'active' => true,
            ],
            [
                'title' => 'مساعد المدير العام',
                'department_slug' => 'مجلس-الإدارة-الإدارة-التنفيذية-المدير-العام',
                'description' => 'يدعم المدير العام في المهام الإدارية',
                'active' => true,
            ],

            // إدارة مراكز الاتصال - مركز الاتصال
            [
                'title' => 'مدير مركز الاتصال',
                'department_slug' => 'إدارة-مراكز-الاتصال-مركز-الاتصال',
                'description' => 'يدير مركز الاتصال',
                'active' => true,
            ],
            [
                'title' => 'مشرف مركز الاتصال',
                'department_slug' => 'إدارة-مراكز-الاتصال-مركز-الاتصال',
                'description' => 'يشرف على عمليات مركز الاتصال',
                'active' => true,
            ],
            [
                'title' => 'موظف مركز الاتصال',
                'department_slug' => 'إدارة-مراكز-الاتصال-مركز-الاتصال',
                'description' => 'يتلقى المكالمات ويديرها',
                'active' => true,
            ],

            // إدارة مراكز الاتصال - خدمة العملاء
            [
                'title' => 'مدير خدمة العملاء',
                'department_slug' => 'إدارة-مراكز-الاتصال-خدمة-العملاء',
                'description' => 'يدير قسم خدمة العملاء',
                'active' => true,
            ],
            [
                'title' => 'ممثل خدمة العملاء',
                'department_slug' => 'إدارة-مراكز-الاتصال-خدمة-العملاء',
                'description' => 'يتعامل مع استفسارات وشكاوى العملاء',
                'active' => true,
            ],

            // إدارة التنسيق
            [
                'title' => 'مدير التنسيق',
                'department_slug' => 'إدارة-التنسيق',
                'description' => 'يدير عمليات التنسيق بين الأقسام',
                'active' => true,
            ],
            [
                'title' => 'منسق',
                'department_slug' => 'إدارة-التنسيق',
                'description' => 'ينسق بين الأقسام المختلفة',
                'active' => true,
            ],

            // إدارة الموارد البشرية - شؤون الموظفين
            [
                'title' => 'مدير شؤون الموظفين',
                'department_slug' => 'إدارة-الموارد-البشرية-شؤون-الموظفين',
                'description' => 'يدير شؤون الموظفين',
                'active' => true,
            ],
            [
                'title' => 'أخصائي شؤون موظفين',
                'department_slug' => 'إدارة-الموارد-البشرية-شؤون-الموظفين',
                'description' => 'يتعامل مع ملفات الموظفين',
                'active' => true,
            ],

            // إدارة الموارد البشرية - التوظيف
            [
                'title' => 'مدير التوظيف',
                'department_slug' => 'إدارة-الموارد-البشرية-التوظيف',
                'description' => 'يدير عمليات التوظيف',
                'active' => true,
            ],
            [
                'title' => 'أخصائي توظيف',
                'department_slug' => 'إدارة-الموارد-البشرية-التوظيف',
                'description' => 'يقوم بعمليات التوظيف والاختيار',
                'active' => true,
            ],

            // إدارة الموارد البشرية - الرواتب
            [
                'title' => 'مدير الرواتب',
                'department_slug' => 'إدارة-الموارد-البشرية-الرواتب',
                'description' => 'يدير قسم الرواتب',
                'active' => true,
            ],
            [
                'title' => 'محاسب رواتب',
                'department_slug' => 'إدارة-الموارد-البشرية-الرواتب',
                'description' => 'يحسب رواتب الموظفين',
                'active' => true,
            ],

            // الإدارة التشغيلية - العمليات
            [
                'title' => 'مدير العمليات',
                'department_slug' => 'الإدارة-التشغيلية-العمليات',
                'description' => 'يدير العمليات التشغيلية',
                'active' => true,
            ],
            [
                'title' => 'مشرف عمليات',
                'department_slug' => 'الإدارة-التشغيلية-العمليات',
                'description' => 'يشرف على العمليات اليومية',
                'active' => true,
            ],

            // الإدارة التشغيلية - الإشراف الميداني
            [
                'title' => 'مدير الإشراف الميداني',
                'department_slug' => 'الإدارة-التشغيلية-الإشراف-الميداني',
                'description' => 'يدير الإشراف الميداني',
                'active' => true,
            ],
            [
                'title' => 'مشرف ميداني',
                'department_slug' => 'الإدارة-التشغيلية-الإشراف-الميداني',
                'description' => 'يشرف على العمليات الميدانية',
                'active' => true,
            ],

            // الإدارة التشغيلية - المتابعة
            [
                'title' => 'مدير المتابعة',
                'department_slug' => 'الإدارة-التشغيلية-المتابعة',
                'description' => 'يدير عمليات المتابعة',
                'active' => true,
            ],
            [
                'title' => 'أخصائي متابعة',
                'department_slug' => 'الإدارة-التشغيلية-المتابعة',
                'description' => 'يتابع تنفيذ المهام والمشاريع',
                'active' => true,
            ],

            // الإدارة الفنية - الصيانة
            [
                'title' => 'مدير الصيانة',
                'department_slug' => 'الإدارة-الفنية-الصيانة',
                'description' => 'يدير قسم الصيانة',
                'active' => true,
            ],
            [
                'title' => 'فني صيانة',
                'department_slug' => 'الإدارة-الفنية-الصيانة',
                'description' => 'يقوم بأعمال الصيانة',
                'active' => true,
            ],

            // الإدارة الفنية - الدعم الفني
            [
                'title' => 'مدير الدعم الفني',
                'department_slug' => 'الإدارة-الفنية-الدعم-الفني',
                'description' => 'يدير قسم الدعم الفني',
                'active' => true,
            ],
            [
                'title' => 'أخصائي دعم فني',
                'department_slug' => 'الإدارة-الفنية-الدعم-الفني',
                'description' => 'يوفر الدعم الفني للمستخدمين',
                'active' => true,
            ],

            // إدارة الجودة - التدقيق
            [
                'title' => 'مدير التدقيق',
                'department_slug' => 'إدارة-الجودة-التدقيق',
                'description' => 'يدير عمليات التدقيق',
                'active' => true,
            ],
            [
                'title' => 'مدقق',
                'department_slug' => 'إدارة-الجودة-التدقيق',
                'description' => 'يقوم بعمليات التدقيق والمراجعة',
                'active' => true,
            ],

            // إدارة الجودة - تحسين الأداء
            [
                'title' => 'مدير تحسين الأداء',
                'department_slug' => 'إدارة-الجودة-تحسين-الأداء',
                'description' => 'يدير برامج تحسين الأداء',
                'active' => true,
            ],
            [
                'title' => 'أخصائي تحسين أداء',
                'department_slug' => 'إدارة-الجودة-تحسين-الأداء',
                'description' => 'يعمل على تحسين أداء العمليات',
                'active' => true,
            ],

            // الإدارة المالية - الحسابات
            [
                'title' => 'مدير الحسابات',
                'department_slug' => 'الإدارة-المالية-الحسابات',
                'description' => 'يدير قسم الحسابات',
                'active' => true,
            ],
            [
                'title' => 'محاسب',
                'department_slug' => 'الإدارة-المالية-الحسابات',
                'description' => 'يقوم بالمهام المحاسبية',
                'active' => true,
            ],

            // الإدارة المالية - المشتريات
            [
                'title' => 'مدير المشتريات',
                'department_slug' => 'الإدارة-المالية-المشتريات',
                'description' => 'يدير قسم المشتريات',
                'active' => true,
            ],
            [
                'title' => 'أخصائي مشتريات',
                'department_slug' => 'الإدارة-المالية-المشتريات',
                'description' => 'يتعامل مع عمليات الشراء',
                'active' => true,
            ],

            // الإدارة القانونية - الشؤون القانونية
            [
                'title' => 'مدير الشؤون القانونية',
                'department_slug' => 'الإدارة-القانونية-الشؤون-القانونية',
                'description' => 'يدير الشؤون القانونية',
                'active' => true,
            ],
            [
                'title' => 'مستشار قانوني',
                'department_slug' => 'الإدارة-القانونية-الشؤون-القانونية',
                'description' => 'يوفر الاستشارات القانونية',
                'active' => true,
            ],

            // إدارة المشاريع - التخطيط
            [
                'title' => 'مدير التخطيط',
                'department_slug' => 'إدارة-المشاريع-التخطيط',
                'description' => 'يدير عمليات التخطيط',
                'active' => true,
            ],
            [
                'title' => 'أخصائي تخطيط',
                'department_slug' => 'إدارة-المشاريع-التخطيط',
                'description' => 'يقوم بتخطيط المشاريع',
                'active' => true,
            ],

            // إدارة المشاريع - التنفيذ
            [
                'title' => 'مدير التنفيذ',
                'department_slug' => 'إدارة-المشاريع-التنفيذ',
                'description' => 'يدير تنفيذ المشاريع',
                'active' => true,
            ],
            [
                'title' => 'مدير مشروع',
                'department_slug' => 'إدارة-المشاريع-التنفيذ',
                'description' => 'يدير مشاريع محددة',
                'active' => true,
            ],

            // الفروع - فرع الرياض
            [
                'title' => 'مدير فرع الرياض',
                'department_slug' => 'الفروع-فرع-الرياض',
                'description' => 'يدير فرع الرياض',
                'active' => true,
            ],
            [
                'title' => 'نائب مدير فرع الرياض',
                'department_slug' => 'الفروع-فرع-الرياض',
                'description' => 'يساعد مدير فرع الرياض',
                'active' => true,
            ],
        ];

        DB::transaction(function () use ($positions) {
            $created = 0;
            $skipped = 0;

            foreach ($positions as $positionData) {
                $department = Department::where('slug', $positionData['department_slug'])->first();

                if (!$department) {
                    $this->command->warn("Department with slug '{$positionData['department_slug']}' not found. Skipping position '{$positionData['title']}'.");
                    $skipped++;
                    continue;
                }

                Position::updateOrCreate(
                    [
                        'title' => $positionData['title'],
                        'department_id' => $department->id,
                    ],
                    [
                        'description' => $positionData['description'] ?? null,
                        'active' => $positionData['active'] ?? true,
                    ]
                );
                $created++;
            }

            $this->command->info("✓ Positions seeded: {$created}");
            if ($skipped > 0) {
                $this->command->warn("⚠ Positions skipped: {$skipped}");
            }
        });

        $this->command->info('✓ HR positions seeding completed');
    }

    /**
     * Generate a unique slug from Arabic name (same logic as DepartmentsSeeder)
     */
    protected function makeSlug(string $arName, ?string $parentSlug = null): string
    {
        $slug = $arName;

        $slug = str_replace('ـ', '', $slug);

        $slug = preg_replace('/[^\p{Arabic}\p{N}\s-]/u', '', $slug);

        $slug = preg_replace('/\s+/', ' ', $slug);
        $slug = trim($slug);

        $slug = str_replace(' ', '-', $slug);

        if ($parentSlug) {
            $slug = $parentSlug . '-' . $slug;
        }

        return $slug;
    }

    /**
     * Get department slug by traversing the tree path
     */
    protected function getDepartmentSlug(array $path): string
    {
        $slug = null;
        foreach ($path as $name) {
            $slug = $this->makeSlug($name, $slug);
        }
        return $slug;
    }
}
