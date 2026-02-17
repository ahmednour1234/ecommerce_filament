<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;

class SidebarMenuBuilder
{
    protected SidebarBadgeService $badgeService;

    public function __construct(SidebarBadgeService $badgeService)
    {
        $this->badgeService = $badgeService;
    }

    public function build(): array
    {
        $config = config('sidebar.items', []);
        return $this->filterItems($config);
    }

    protected function filterItems(array $items): array
    {
        $filtered = [];

        foreach ($items as $item) {
            $filteredItem = $this->processItem($item);

            if ($filteredItem !== null) {
                $filtered[] = $filteredItem;
            }
        }

        return $filtered;
    }

    protected function processItem(array $item): ?array
    {
        if (isset($item['permission']) && $item['permission'] !== null) {
            $user = Auth::user();
            if (!Auth::check() || (!$user->hasRole('super_admin') && !$user->can($item['permission']))) {
                return null;
            }
        }

        $processed = [
            'title' => $item['title'],
            'icon' => $item['icon'] ?? null,
            'url' => $this->resolveUrl($item['url'] ?? null),
            'badge' => $this->resolveBadge($item['badge'] ?? null),
            'children' => [],
        ];

        if (isset($item['children']) && is_array($item['children'])) {
            $processed['children'] = $this->filterItems($item['children']);

            if (empty($processed['children']) && $processed['url'] === null) {
                return null;
            }
        }

        return $processed;
    }

    protected function resolveUrl($url): ?string
    {
        if ($url === null) {
            return null;
        }

        if (is_callable($url)) {
            try {
                return $url();
            } catch (\Exception $e) {
                return null;
            }
        }

        if (is_string($url)) {
            return $url;
        }

        return null;
    }

    protected function resolveBadge(?string $badgeKey): ?int
    {
        if ($badgeKey === null) {
            return null;
        }

        return $this->badgeService->getCount($badgeKey);
    }
}
