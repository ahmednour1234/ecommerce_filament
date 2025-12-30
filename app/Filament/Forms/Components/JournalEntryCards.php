<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Component;
use Filament\Forms\Components\Concerns\HasState;

class JournalEntryCards extends Component
{
    use HasState;

    protected string $view = 'filament.forms.components.journal-entry-cards';

    protected ?array $gridColumns = [];
    protected bool $allowAddRows = true;
    protected bool $allowDeleteRows = true;
    protected bool $allowQuickAdd = true;
    protected int $quickAddCount = 10;
    protected ?string $totalDebitColumn = null;
    protected ?string $totalCreditColumn = null;
    protected ?string $differenceColumn = null;

    public static function make(string $name): static
    {
        $static = app(static::class, ['name' => $name]);
        $static->configure();

        return $static;
    }

    public function setColumns(array $columns): static
    {
        $this->gridColumns = $columns;
        return $this;
    }

    public function allowAddRows(bool $allow = true): static
    {
        $this->allowAddRows = $allow;
        return $this;
    }

    public function allowDeleteRows(bool $allow = true): static
    {
        $this->allowDeleteRows = $allow;
        return $this;
    }

    public function allowQuickAdd(bool $allow = true): static
    {
        $this->allowQuickAdd = $allow;
        return $this;
    }

    public function quickAddCount(int $count): static
    {
        $this->quickAddCount = $count;
        return $this;
    }

    public function totalDebitColumn(string $column): static
    {
        $this->totalDebitColumn = $column;
        return $this;
    }

    public function totalCreditColumn(string $column): static
    {
        $this->totalCreditColumn = $column;
        return $this;
    }

    public function differenceColumn(string $column): static
    {
        $this->differenceColumn = $column;
        return $this;
    }

    public function getGridColumns(): array
    {
        return $this->gridColumns ?? [];
    }

    public function getAllowAddRows(): bool
    {
        return $this->allowAddRows;
    }

    public function getAllowDeleteRows(): bool
    {
        return $this->allowDeleteRows;
    }

    public function getAllowQuickAdd(): bool
    {
        return $this->allowQuickAdd;
    }

    public function getQuickAddCount(): int
    {
        return $this->quickAddCount;
    }

    public function getTotalDebitColumn(): ?string
    {
        return $this->totalDebitColumn;
    }

    public function getTotalCreditColumn(): ?string
    {
        return $this->totalCreditColumn;
    }

    public function getDifferenceColumn(): ?string
    {
        return $this->differenceColumn;
    }
}

