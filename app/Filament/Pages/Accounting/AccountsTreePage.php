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
    public $expandedAccounts = [];

    public function mount(): void
    {
        $this->loadAccounts();
        // Expand all accounts by default
        $this->expandAll();
    }

    public function updatedSelectedAccountType(): void
    {
        $this->loadAccounts();
        // Reset and expand all accounts after filtering
        $this->expandAll();
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

    public function toggleAccount($accountId): void
    {
        if (in_array($accountId, $this->expandedAccounts)) {
            $this->expandedAccounts = array_values(array_diff($this->expandedAccounts, [$accountId]));
        } else {
            $this->expandedAccounts[] = $accountId;
        }
    }

    public function isExpanded($accountId): bool
    {
        return in_array($accountId, $this->expandedAccounts);
    }

    protected function expandAll(): void
    {
        $this->expandedAccounts = [];
        $this->collectAccountIds($this->accounts);
    }

    protected function collectAccountIds($accounts): void
    {
        foreach ($accounts as $item) {
            $account = $item['account'];
            $children = $item['children'] ?? [];
            
            if (count($children) > 0) {
                $this->expandedAccounts[] = $account->id;
                $this->collectAccountIds($children);
            }
        }
    }

    protected function getViewData(): array
    {
        return [
            'accounts' => $this->accounts,
        ];
    }
}
