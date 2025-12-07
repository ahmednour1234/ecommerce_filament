<?php

namespace App\Filament\Pages\Accounting;

use App\Models\Accounting\Account;
use Filament\Pages\Page;

class AccountsTreePage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationGroup = 'Accounting';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.accounting.accounts-tree';

    public $accounts = [];
    public $selectedAccountType = 'all';

    public function mount(): void
    {
        $this->loadAccounts();
    }

    public function updatedSelectedAccountType(): void
    {
        $this->loadAccounts();
    }

    protected function loadAccounts(): void
    {
        $query = Account::query();

        if ($this->selectedAccountType !== 'all') {
            $query->where('type', $this->selectedAccountType);
        }

        $allAccounts = $query->orderBy('code')->get();
        
        // Build tree structure
        $this->accounts = $this->buildTree($allAccounts);
    }

    protected function buildTree($accounts, $parentId = null): array
    {
        $tree = [];

        foreach ($accounts as $account) {
            if ($account->parent_id === $parentId) {
                $children = $this->buildTree($accounts, $account->id);
                $tree[] = [
                    'account' => $account,
                    'children' => $children,
                ];
            }
        }

        return $tree;
    }

    protected function getViewData(): array
    {
        return [
            'accounts' => $this->accounts,
        ];
    }
}
