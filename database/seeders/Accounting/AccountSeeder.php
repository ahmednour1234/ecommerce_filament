<?php

namespace Database\Seeders\Accounting;

use App\Models\Accounting\Account;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        // Assets
        $assets = Account::updateOrCreate(
            ['code' => '1'],
            [
                'name' => 'Assets',
                'type' => 'asset',
                'level' => 1,
                'is_active' => true,
                'allow_manual_entry' => false,
            ]
        );

        $currentAssets = Account::updateOrCreate(
            ['code' => '1.1'],
            [
                'name' => 'Current Assets',
                'type' => 'asset',
                'parent_id' => $assets->id,
                'level' => 2,
                'is_active' => true,
                'allow_manual_entry' => false,
            ]
        );

        Account::updateOrCreate(
            ['code' => '1.1.1'],
            [
                'name' => 'Cash',
                'type' => 'asset',
                'parent_id' => $currentAssets->id,
                'level' => 3,
                'is_active' => true,
                'allow_manual_entry' => true,
            ]
        );

        Account::updateOrCreate(
            ['code' => '1.1.2'],
            [
                'name' => 'Accounts Receivable',
                'type' => 'asset',
                'parent_id' => $currentAssets->id,
                'level' => 3,
                'is_active' => true,
                'allow_manual_entry' => true,
            ]
        );

        // Liabilities
        $liabilities = Account::updateOrCreate(
            ['code' => '2'],
            [
                'name' => 'Liabilities',
                'type' => 'liability',
                'level' => 1,
                'is_active' => true,
                'allow_manual_entry' => false,
            ]
        );

        $currentLiabilities = Account::updateOrCreate(
            ['code' => '2.1'],
            [
                'name' => 'Current Liabilities',
                'type' => 'liability',
                'parent_id' => $liabilities->id,
                'level' => 2,
                'is_active' => true,
                'allow_manual_entry' => false,
            ]
        );

        Account::updateOrCreate(
            ['code' => '2.1.1'],
            [
                'name' => 'Accounts Payable',
                'type' => 'liability',
                'parent_id' => $currentLiabilities->id,
                'level' => 3,
                'is_active' => true,
                'allow_manual_entry' => true,
            ]
        );

        // Revenue
        $revenue = Account::updateOrCreate(
            ['code' => '4'],
            [
                'name' => 'Revenue',
                'type' => 'revenue',
                'level' => 1,
                'is_active' => true,
                'allow_manual_entry' => false,
            ]
        );

        Account::updateOrCreate(
            ['code' => '4.1'],
            [
                'name' => 'Sales Revenue',
                'type' => 'revenue',
                'parent_id' => $revenue->id,
                'level' => 2,
                'is_active' => true,
                'allow_manual_entry' => true,
            ]
        );

        // Expenses
        $expenses = Account::updateOrCreate(
            ['code' => '5'],
            [
                'name' => 'Expenses',
                'type' => 'expense',
                'level' => 1,
                'is_active' => true,
                'allow_manual_entry' => false,
            ]
        );

        Account::updateOrCreate(
            ['code' => '5.1'],
            [
                'name' => 'Cost of Goods Sold',
                'type' => 'expense',
                'parent_id' => $expenses->id,
                'level' => 2,
                'is_active' => true,
                'allow_manual_entry' => true,
            ]
        );

        Account::updateOrCreate(
            ['code' => '5.2'],
            [
                'name' => 'Operating Expenses',
                'type' => 'expense',
                'parent_id' => $expenses->id,
                'level' => 2,
                'is_active' => true,
                'allow_manual_entry' => true,
            ]
        );
    }
}

