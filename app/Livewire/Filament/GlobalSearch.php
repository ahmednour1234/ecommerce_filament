<?php

namespace App\Livewire\Filament;

use App\Services\GlobalSearchService;
use Livewire\Component;

class GlobalSearch extends Component
{
    public string $query = '';
    public array $results = [];
    public bool $isOpen = false;
    public int $selectedIndex = -1;

    public function updatedQuery(): void
    {
        $this->selectedIndex = -1;

        if (empty(trim($this->query))) {
            $this->results = [];
            $this->isOpen = false;
            return;
        }

        $this->performSearch();
    }

    public function performSearch(): void
    {
        $service = app(GlobalSearchService::class);
        $this->results = $service->search($this->query);
        $this->isOpen = !empty($this->results);
    }

    public function selectResult(int $index): void
    {
        if (!isset($this->results[$index])) {
            return;
        }

        $result = $this->results[$index];
        $this->redirect($result['url'], navigate: true);
    }

    public function selectFirstResult(): void
    {
        if (!empty($this->results)) {
            $this->selectResult(0);
        }
    }

    public function close(): void
    {
        $this->isOpen = false;
        $this->selectedIndex = -1;
    }

    public function open(): void
    {
        if (!empty($this->results)) {
            $this->isOpen = true;
        }
    }

    public function clear(): void
    {
        $this->query = '';
        $this->results = [];
        $this->isOpen = false;
        $this->selectedIndex = -1;
    }

    public function moveSelection(int $direction): void
    {
        if (empty($this->results)) {
            return;
        }

        $this->selectedIndex += $direction;

        if ($this->selectedIndex < 0) {
            $this->selectedIndex = count($this->results) - 1;
        } elseif ($this->selectedIndex >= count($this->results)) {
            $this->selectedIndex = 0;
        }
    }

    public function render()
    {
        return view('livewire.filament.global-search');
    }
}
