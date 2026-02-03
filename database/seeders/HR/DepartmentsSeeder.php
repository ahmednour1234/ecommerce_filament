<?php

namespace Database\Seeders\HR;

use App\Models\HR\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DepartmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding HR departments...');

        $departmentsTree = [
            [
                'name' => 'مجلس الإدارة',
                'children' => [
                    [
                        'name' => 'الإدارة التنفيذية',
                        'children' => [
                            ['name' => 'المدير العام'],
                        ],
                    ],
                ],
            ],
            [
                'name' => 'إدارة مراكز الاتصال',
                'children' => [
                    ['name' => 'مركز الاتصال'],
                    ['name' => 'خدمة العملاء'],
                ],
            ],
            [
                'name' => 'إدارة التنسيق',
            ],
            [
                'name' => 'إدارة الموارد البشرية',
                'children' => [
                    ['name' => 'شؤون الموظفين'],
                    ['name' => 'التوظيف'],
                    ['name' => 'الرواتب'],
                ],
            ],
            [
                'name' => 'الإدارة التشغيلية',
                'children' => [
                    ['name' => 'العمليات'],
                    ['name' => 'الإشراف الميداني'],
                    ['name' => 'المتابعة'],
                ],
            ],
            [
                'name' => 'الإدارة الفنية',
                'children' => [
                    ['name' => 'الصيانة'],
                    ['name' => 'الدعم الفني'],
                ],
            ],
            [
                'name' => 'إدارة الجودة',
                'children' => [
                    ['name' => 'التدقيق'],
                    ['name' => 'تحسين الأداء'],
                ],
            ],
            [
                'name' => 'الإدارة المالية',
                'children' => [
                    ['name' => 'الحسابات'],
                    ['name' => 'المشتريات'],
                ],
            ],
            [
                'name' => 'الإدارة القانونية',
                'children' => [
                    ['name' => 'الشؤون القانونية'],
                ],
            ],
            [
                'name' => 'إدارة المشاريع',
                'children' => [
                    ['name' => 'التخطيط'],
                    ['name' => 'التنفيذ'],
                ],
            ],
            [
                'name' => 'الفروع',
                'children' => [
                    ['name' => 'فرع الرياض'],
                ],
            ],
        ];

        DB::transaction(function () use ($departmentsTree) {
            foreach ($departmentsTree as $node) {
                $this->seedNode($node);
            }
        });

        $this->command->info('✓ HR departments seeding completed');
    }

    /**
     * Recursively seed a department node and its children
     */
    protected function seedNode(array $node, ?int $parentId = null, ?string $parentSlug = null): void
    {
        $name = $node['name'];
        $slug = $this->makeSlug($name, $parentSlug);

        $department = Department::updateOrCreate(
            ['slug' => $slug],
            [
                'name' => $name,
                'parent_id' => $parentId,
                'active' => true,
            ]
        );

        if (isset($node['children']) && is_array($node['children'])) {
            foreach ($node['children'] as $child) {
                $this->seedNode($child, $department->id, $slug);
            }
        }
    }

    /**
     * Generate a unique slug from Arabic name
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
}
