<?php

namespace Database\Seeders\Accounting;

use App\Models\Accounting\Account;
use Illuminate\Database\Seeder;

class Level5AccountsArabicSeeder extends Seeder
{
    /**
     * Seed level 5 accounts with Arabic names for all account types
     */
    public function run(): void
    {
        $this->command->info('Seeding Level 5 Accounts with Arabic Names...');

        // Get or create level 4 parent accounts for each type
        $this->seedAssetAccounts();
        $this->seedLiabilityAccounts();
        $this->seedEquityAccounts();
        $this->seedRevenueAccounts();
        $this->seedExpenseAccounts();

        $this->command->info('Level 5 Accounts with Arabic Names seeded successfully!');
    }

    /**
     * Seed Asset accounts (level 5)
     */
    protected function seedAssetAccounts(): void
    {
        // Find or create level 4 parent accounts
        $cashParent = $this->getOrCreateLevel4Account('1.1.1.1', 'النقدية', 'asset', '1.1.1');
        $receivablesParent = $this->getOrCreateLevel4Account('1.1.2.1', 'الذمم المدينة', 'asset', '1.1.2');
        $inventoryParent = $this->getOrCreateLevel4Account('1.1.3.1', 'المخزون', 'asset', '1.1.3');
        $fixedAssetsParent = $this->getOrCreateLevel4Account('1.2.1.1', 'الأصول الثابتة', 'asset', '1.2.1');

        // Level 5 Asset Accounts
        $assetAccounts = [
            // Cash accounts
            ['code' => '1.1.1.1.1', 'name' => 'الصندوق الرئيسي', 'parent' => $cashParent],
            ['code' => '1.1.1.1.2', 'name' => 'البنك الأهلي', 'parent' => $cashParent],
            ['code' => '1.1.1.1.3', 'name' => 'البنك العربي', 'parent' => $cashParent],
            ['code' => '1.1.1.1.4', 'name' => 'البنك التجاري', 'parent' => $cashParent],
            ['code' => '1.1.1.1.5', 'name' => 'سندات الخزينة', 'parent' => $cashParent],

            // Receivables accounts
            ['code' => '1.1.2.1.1', 'name' => 'العملاء - محلي', 'parent' => $receivablesParent],
            ['code' => '1.1.2.1.2', 'name' => 'العملاء - خارجي', 'parent' => $receivablesParent],
            ['code' => '1.1.2.1.3', 'name' => 'أوراق القبض', 'parent' => $receivablesParent],
            ['code' => '1.1.2.1.4', 'name' => 'مدينون آخرون', 'parent' => $receivablesParent],
            ['code' => '1.1.2.1.5', 'name' => 'مخصص ديون مشكوك فيها', 'parent' => $receivablesParent],

            // Inventory accounts
            ['code' => '1.1.3.1.1', 'name' => 'مخزون البضائع التامة', 'parent' => $inventoryParent],
            ['code' => '1.1.3.1.2', 'name' => 'مخزون المواد الخام', 'parent' => $inventoryParent],
            ['code' => '1.1.3.1.3', 'name' => 'مخزون الإنتاج تحت التشغيل', 'parent' => $inventoryParent],
            ['code' => '1.1.3.1.4', 'name' => 'مخزون البضائع قيد النقل', 'parent' => $inventoryParent],
            ['code' => '1.1.3.1.5', 'name' => 'مخزون البضائع التالفة', 'parent' => $inventoryParent],

            // Fixed Assets accounts
            ['code' => '1.2.1.1.1', 'name' => 'الأراضي', 'parent' => $fixedAssetsParent],
            ['code' => '1.2.1.1.2', 'name' => 'المباني', 'parent' => $fixedAssetsParent],
            ['code' => '1.2.1.1.3', 'name' => 'الآلات والمعدات', 'parent' => $fixedAssetsParent],
            ['code' => '1.2.1.1.4', 'name' => 'وسائل النقل', 'parent' => $fixedAssetsParent],
            ['code' => '1.2.1.1.5', 'name' => 'معدات المكتب', 'parent' => $fixedAssetsParent],
        ];

        $this->createLevel5Accounts($assetAccounts, 'asset');
    }

    /**
     * Seed Liability accounts (level 5)
     */
    protected function seedLiabilityAccounts(): void
    {
        // Find or create level 4 parent accounts
        $payablesParent = $this->getOrCreateLevel4Account('2.1.1.1', 'الذمم الدائنة', 'liability', '2.1.1');
        $loansParent = $this->getOrCreateLevel4Account('2.1.2.1', 'القروض', 'liability', '2.1.2');
        $taxesParent = $this->getOrCreateLevel4Account('2.1.3.1', 'الضرائب', 'liability', '2.1.3');
        $longTermParent = $this->getOrCreateLevel4Account('2.2.1.1', 'الالتزامات طويلة الأجل', 'liability', '2.2.1');

        // Level 5 Liability Accounts
        $liabilityAccounts = [
            // Payables accounts
            ['code' => '2.1.1.1.1', 'name' => 'الموردون - محلي', 'parent' => $payablesParent],
            ['code' => '2.1.1.1.2', 'name' => 'الموردون - خارجي', 'parent' => $payablesParent],
            ['code' => '2.1.1.1.3', 'name' => 'أوراق الدفع', 'parent' => $payablesParent],
            ['code' => '2.1.1.1.4', 'name' => 'دائنون آخرون', 'parent' => $payablesParent],
            ['code' => '2.1.1.1.5', 'name' => 'المصروفات المستحقة', 'parent' => $payablesParent],

            // Loans accounts
            ['code' => '2.1.2.1.1', 'name' => 'قرض بنكي قصير الأجل', 'parent' => $loansParent],
            ['code' => '2.1.2.1.2', 'name' => 'قرض تجاري', 'parent' => $loansParent],
            ['code' => '2.1.2.1.3', 'name' => 'قرض شخصي', 'parent' => $loansParent],
            ['code' => '2.1.2.1.4', 'name' => 'قرض حكومي', 'parent' => $loansParent],
            ['code' => '2.1.2.1.5', 'name' => 'فوائد مستحقة الدفع', 'parent' => $loansParent],

            // Taxes accounts
            ['code' => '2.1.3.1.1', 'name' => 'ضريبة القيمة المضافة مستحقة الدفع', 'parent' => $taxesParent],
            ['code' => '2.1.3.1.2', 'name' => 'ضريبة الدخل مستحقة الدفع', 'parent' => $taxesParent],
            ['code' => '2.1.3.1.3', 'name' => 'ضريبة المبيعات مستحقة الدفع', 'parent' => $taxesParent],
            ['code' => '2.1.3.1.4', 'name' => 'الضرائب المقتطعة', 'parent' => $taxesParent],
            ['code' => '2.1.3.1.5', 'name' => 'الرسوم الحكومية', 'parent' => $taxesParent],

            // Long-term liabilities
            ['code' => '2.2.1.1.1', 'name' => 'قرض بنكي طويل الأجل', 'parent' => $longTermParent],
            ['code' => '2.2.1.1.2', 'name' => 'سندات مستحقة الدفع', 'parent' => $longTermParent],
            ['code' => '2.2.1.1.3', 'name' => 'التزامات التقاعد', 'parent' => $longTermParent],
            ['code' => '2.2.1.1.4', 'name' => 'التزامات التأمين', 'parent' => $longTermParent],
            ['code' => '2.2.1.1.5', 'name' => 'التزامات أخرى طويلة الأجل', 'parent' => $longTermParent],
        ];

        $this->createLevel5Accounts($liabilityAccounts, 'liability');
    }

    /**
     * Seed Equity accounts (level 5)
     */
    protected function seedEquityAccounts(): void
    {
        // Find or create level 4 parent accounts
        $capitalParent = $this->getOrCreateLevel4Account('3.1.1.1', 'رأس المال', 'equity', '3.1.1');
        $reservesParent = $this->getOrCreateLevel4Account('3.1.2.1', 'الاحتياطيات', 'equity', '3.1.2');
        $retainedEarningsParent = $this->getOrCreateLevel4Account('3.1.3.1', 'الأرباح المحتجزة', 'equity', '3.1.3');

        // Level 5 Equity Accounts
        $equityAccounts = [
            // Capital accounts
            ['code' => '3.1.1.1.1', 'name' => 'رأس المال المدفوع', 'parent' => $capitalParent],
            ['code' => '3.1.1.1.2', 'name' => 'رأس المال المصرح به', 'parent' => $capitalParent],
            ['code' => '3.1.1.1.3', 'name' => 'رأس المال المطلوب', 'parent' => $capitalParent],
            ['code' => '3.1.1.1.4', 'name' => 'أسهم الخزينة', 'parent' => $capitalParent],
            ['code' => '3.1.1.1.5', 'name' => 'رأس المال الإضافي المدفوع', 'parent' => $capitalParent],

            // Reserves accounts
            ['code' => '3.1.2.1.1', 'name' => 'احتياطي قانوني', 'parent' => $reservesParent],
            ['code' => '3.1.2.1.2', 'name' => 'احتياطي عام', 'parent' => $reservesParent],
            ['code' => '3.1.2.1.3', 'name' => 'احتياطي خاص', 'parent' => $reservesParent],
            ['code' => '3.1.2.1.4', 'name' => 'احتياطي إعادة التقييم', 'parent' => $reservesParent],
            ['code' => '3.1.2.1.5', 'name' => 'احتياطي آخر', 'parent' => $reservesParent],

            // Retained Earnings accounts
            ['code' => '3.1.3.1.1', 'name' => 'أرباح السنة الحالية', 'parent' => $retainedEarningsParent],
            ['code' => '3.1.3.1.2', 'name' => 'أرباح السنوات السابقة', 'parent' => $retainedEarningsParent],
            ['code' => '3.1.3.1.3', 'name' => 'خسائر متراكمة', 'parent' => $retainedEarningsParent],
            ['code' => '3.1.3.1.4', 'name' => 'توزيعات الأرباح', 'parent' => $retainedEarningsParent],
            ['code' => '3.1.3.1.5', 'name' => 'تسوية الأرباح والخسائر', 'parent' => $retainedEarningsParent],
        ];

        $this->createLevel5Accounts($equityAccounts, 'equity');
    }

    /**
     * Seed Revenue accounts (level 5)
     */
    protected function seedRevenueAccounts(): void
    {
        // Find or create level 4 parent accounts
        $salesParent = $this->getOrCreateLevel4Account('4.1.1.1', 'مبيعات', 'revenue', '4.1.1');
        $servicesParent = $this->getOrCreateLevel4Account('4.1.2.1', 'إيرادات الخدمات', 'revenue', '4.1.2');
        $otherRevenueParent = $this->getOrCreateLevel4Account('4.1.3.1', 'إيرادات أخرى', 'revenue', '4.1.3');

        // Level 5 Revenue Accounts
        $revenueAccounts = [
            // Sales accounts
            ['code' => '4.1.1.1.1', 'name' => 'مبيعات البضائع - محلي', 'parent' => $salesParent],
            ['code' => '4.1.1.1.2', 'name' => 'مبيعات البضائع - تصدير', 'parent' => $salesParent],
            ['code' => '4.1.1.1.3', 'name' => 'مبيعات الجملة', 'parent' => $salesParent],
            ['code' => '4.1.1.1.4', 'name' => 'مبيعات التجزئة', 'parent' => $salesParent],
            ['code' => '4.1.1.1.5', 'name' => 'خصومات المبيعات', 'parent' => $salesParent],

            // Services accounts
            ['code' => '4.1.2.1.1', 'name' => 'إيرادات الخدمات الاستشارية', 'parent' => $servicesParent],
            ['code' => '4.1.2.1.2', 'name' => 'إيرادات الخدمات الفنية', 'parent' => $servicesParent],
            ['code' => '4.1.2.1.3', 'name' => 'إيرادات الخدمات الإدارية', 'parent' => $servicesParent],
            ['code' => '4.1.2.1.4', 'name' => 'إيرادات الإيجار', 'parent' => $servicesParent],
            ['code' => '4.1.2.1.5', 'name' => 'إيرادات الفوائد', 'parent' => $servicesParent],

            // Other Revenue accounts
            ['code' => '4.1.3.1.1', 'name' => 'إيرادات الاستثمارات', 'parent' => $otherRevenueParent],
            ['code' => '4.1.3.1.2', 'name' => 'إيرادات الصرف', 'parent' => $otherRevenueParent],
            ['code' => '4.1.3.1.3', 'name' => 'إيرادات أخرى متنوعة', 'parent' => $otherRevenueParent],
            ['code' => '4.1.3.1.4', 'name' => 'إيرادات غير متكررة', 'parent' => $otherRevenueParent],
            ['code' => '4.1.3.1.5', 'name' => 'إيرادات استثنائية', 'parent' => $otherRevenueParent],
        ];

        $this->createLevel5Accounts($revenueAccounts, 'revenue');
    }

    /**
     * Seed Expense accounts (level 5)
     */
    protected function seedExpenseAccounts(): void
    {
        // Find or create level 4 parent accounts
        $cogsParent = $this->getOrCreateLevel4Account('5.1.1.1', 'تكلفة البضاعة المباعة', 'expense', '5.1.1');
        $operatingExpensesParent = $this->getOrCreateLevel4Account('5.2.1.1', 'المصروفات التشغيلية', 'expense', '5.2.1');
        $adminExpensesParent = $this->getOrCreateLevel4Account('5.2.2.1', 'المصروفات الإدارية', 'expense', '5.2.2');
        $financialExpensesParent = $this->getOrCreateLevel4Account('5.3.1.1', 'المصروفات المالية', 'expense', '5.3.1');

        // Level 5 Expense Accounts
        $expenseAccounts = [
            // Cost of Goods Sold accounts
            ['code' => '5.1.1.1.1', 'name' => 'تكلفة البضاعة المشتراة', 'parent' => $cogsParent],
            ['code' => '5.1.1.1.2', 'name' => 'تكلفة المواد الخام', 'parent' => $cogsParent],
            ['code' => '5.1.1.1.3', 'name' => 'تكلفة العمالة المباشرة', 'parent' => $cogsParent],
            ['code' => '5.1.1.1.4', 'name' => 'التكاليف الصناعية غير المباشرة', 'parent' => $cogsParent],
            ['code' => '5.1.1.1.5', 'name' => 'تكلفة النقل والتخزين', 'parent' => $cogsParent],

            // Operating Expenses accounts
            ['code' => '5.2.1.1.1', 'name' => 'رواتب الموظفين', 'parent' => $operatingExpensesParent],
            ['code' => '5.2.1.1.2', 'name' => 'بدلات الموظفين', 'parent' => $operatingExpensesParent],
            ['code' => '5.2.1.1.3', 'name' => 'مصروفات الإعلان', 'parent' => $operatingExpensesParent],
            ['code' => '5.2.1.1.4', 'name' => 'مصروفات التسويق', 'parent' => $operatingExpensesParent],
            ['code' => '5.2.1.1.5', 'name' => 'مصروفات المبيعات', 'parent' => $operatingExpensesParent],

            // Administrative Expenses accounts
            ['code' => '5.2.2.1.1', 'name' => 'رواتب الإدارة', 'parent' => $adminExpensesParent],
            ['code' => '5.2.2.1.2', 'name' => 'مصروفات المكتب', 'parent' => $adminExpensesParent],
            ['code' => '5.2.2.1.3', 'name' => 'مصروفات الكهرباء والماء', 'parent' => $adminExpensesParent],
            ['code' => '5.2.2.1.4', 'name' => 'مصروفات الصيانة', 'parent' => $adminExpensesParent],
            ['code' => '5.2.2.1.5', 'name' => 'مصروفات التأمين', 'parent' => $adminExpensesParent],

            // Financial Expenses accounts
            ['code' => '5.3.1.1.1', 'name' => 'فوائد القروض', 'parent' => $financialExpensesParent],
            ['code' => '5.3.1.1.2', 'name' => 'مصروفات البنك', 'parent' => $financialExpensesParent],
            ['code' => '5.3.1.1.3', 'name' => 'خسائر الصرف', 'parent' => $financialExpensesParent],
            ['code' => '5.3.1.1.4', 'name' => 'مصروفات الخصم', 'parent' => $financialExpensesParent],
            ['code' => '5.3.1.1.5', 'name' => 'مصروفات مالية أخرى', 'parent' => $financialExpensesParent],
        ];

        $this->createLevel5Accounts($expenseAccounts, 'expense');
    }

    /**
     * Get or create a level 4 account
     */
    protected function getOrCreateLevel4Account(string $code, string $name, string $type, string $parentCode): Account
    {
        // Try to find existing level 4 account
        $level4Account = Account::where('code', $code)->where('level', 4)->first();

        if ($level4Account) {
            return $level4Account;
        }

        // Find parent account (level 3)
        $parent = Account::where('code', $parentCode)->where('level', 3)->first();

        // If parent doesn't exist, try to find level 2 or 1
        if (!$parent) {
            $parent = Account::where('code', $parentCode)->first();
        }

        // If still no parent, create a level 3 parent first
        if (!$parent) {
            $parent = $this->createParentAccount($parentCode, $type);
        }

        // Create level 4 account
        return Account::updateOrCreate(
            ['code' => $code],
            [
                'name' => $name,
                'type' => $type,
                'parent_id' => $parent->id,
                'level' => 4,
                'is_active' => true,
                'allow_manual_entry' => false,
            ]
        );
    }

    /**
     * Create a parent account if it doesn't exist
     */
    protected function createParentAccount(string $code, string $type): Account
    {
        $parts = explode('.', $code);
        $level = count($parts);

        // Determine parent code
        $parentCode = null;
        if ($level > 1) {
            array_pop($parts);
            $parentCode = implode('.', $parts);
        }

        $parent = null;
        if ($parentCode) {
            $parent = Account::where('code', $parentCode)->first();
            if (!$parent) {
                $parent = $this->createParentAccount($parentCode, $type);
            }
        }

        // Generate a default name based on code
        $defaultNames = [
            'asset' => 'أصول',
            'liability' => 'التزامات',
            'equity' => 'حقوق الملكية',
            'revenue' => 'إيرادات',
            'expense' => 'مصروفات',
        ];

        $name = $defaultNames[$type] ?? 'حساب';

        return Account::updateOrCreate(
            ['code' => $code],
            [
                'name' => $name . ' - ' . $code,
                'type' => $type,
                'parent_id' => $parent?->id,
                'level' => $level,
                'is_active' => true,
                'allow_manual_entry' => false,
            ]
        );
    }

    /**
     * Create level 5 accounts
     */
    protected function createLevel5Accounts(array $accounts, string $type): void
    {
        foreach ($accounts as $accountData) {
            Account::updateOrCreate(
                ['code' => $accountData['code']],
                [
                    'name' => $accountData['name'],
                    'type' => $type,
                    'parent_id' => $accountData['parent']->id,
                    'level' => 5,
                    'is_active' => true,
                    'allow_manual_entry' => true,
                ]
            );
        }

        $this->command->info("Created " . count($accounts) . " level 5 {$type} accounts");
    }
}

