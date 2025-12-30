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
    public $selectedAccountId = null;
    public $searchTerm = '';

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
        $this->selectedAccountId = null;
    }

    public function updatedSearchTerm(): void
    {
        $this->loadAccounts();
        $this->expandAll();
    }

    protected function loadAccounts(): void
    {
        $query = Account::query();

        if ($this->selectedAccountType !== 'all') {
            $query->where('type', $this->selectedAccountType);
        }

        if ($this->searchTerm) {
            $query->where(function ($q) {
                $q->where('code', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('name', 'like', '%' . $this->searchTerm . '%');
            });
        }

        $allAccounts = $query->orderBy('code')->get();
        
        // Build tree structure
        $this->accounts = $this->buildTree($allAccounts);
    }

    public function selectAccount($accountId): void
    {
        $this->selectedAccountId = $accountId;
    }

    public function getSelectedAccountProperty()
    {
        if (!$this->selectedAccountId) {
            return null;
        }
        
        return Account::with('parent')->find($this->selectedAccountId);
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

    public function exportToExcel()
    {
        $accounts = Account::orderBy('code')->get();
        
        $filename = 'accounts_export_' . date('Y-m-d_His') . '.csv';
        $filepath = storage_path('app/temp/' . $filename);
        
        // Ensure temp directory exists
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }
        
        $file = fopen($filepath, 'w');
        
        // Add BOM for UTF-8
        fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Headers
        fputcsv($file, [
            'Code',
            'Name',
            'Type',
            'Parent Account',
            'Level',
            'Active',
            'Allow Manual Entry',
            'Notes',
        ]);

        // Data
        foreach ($accounts as $account) {
            fputcsv($file, [
                $account->code,
                $account->name,
                ucfirst($account->type),
                $account->parent ? $account->parent->name : '',
                $account->level,
                $account->is_active ? 'Yes' : 'No',
                $account->allow_manual_entry ? 'Yes' : 'No',
                $account->notes ?? '',
            ]);
        }

        fclose($file);
        
        return $this->download($filepath, $filename);
    }

    public function resetFilters(): void
    {
        $this->selectedAccountType = 'all';
        $this->searchTerm = '';
        $this->selectedAccountId = null;
        $this->loadAccounts();
        $this->expandAll();
    }

    public function deleteAccount($accountId): void
    {
        $account = Account::find($accountId);
        
        if ($account && auth()->user()?->can('accounts.delete')) {
            // Check if account has children
            if ($account->children()->count() > 0) {
                session()->flash('error', 'Cannot delete account with child accounts. Please delete child accounts first.');
                return;
            }
            
            $account->delete();
            $this->selectedAccountId = null;
            $this->loadAccounts();
            $this->expandAll();
            session()->flash('success', 'Account deleted successfully.');
        }
    }

    protected function getViewData(): array
    {
        return [
            'accounts' => $this->accounts,
        ];
    }
}
