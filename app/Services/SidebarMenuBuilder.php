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
            if (!Auth::check()) {
                return null;
            }
            
            if (!$user->hasRole('super_admin') && !$user->can($item['permission'])) {
                \Log::debug('Sidebar item filtered by permission', [
                    'title' => $item['title'] ?? 'unknown',
                    'permission' => $item['permission'],
                    'user_id' => $user->id,
                    'has_permission' => $user->can($item['permission']),
                    'is_super_admin' => $user->hasRole('super_admin')
                ]);
                return null;
            }
        }

        $resolvedUrl = $this->resolveUrl($item['url'] ?? null);
        
        $processed = [
            'title' => $item['title'],
            'icon' => $item['icon'] ?? null,
            'url' => $resolvedUrl,
            'badge' => $this->resolveBadge($item['badge'] ?? null),
            'children' => [],
        ];

        if (isset($item['children']) && is_array($item['children'])) {
            $processed['children'] = $this->filterItems($item['children']);

            if (empty($processed['children']) && $processed['url'] === null) {
                return null;
            }
        }

        if ($processed['url'] === null && empty($processed['children'])) {
            \Log::debug('Sidebar item filtered: no URL and no children', [
                'title' => $item['title'] ?? 'unknown'
            ]);
            return null;
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
                $resolvedUrl = $url();
                if ($resolvedUrl === null || $resolvedUrl === '') {
                    \Log::debug('Sidebar URL resolved to empty', ['url' => $url]);
                    return null;
                }
                return $resolvedUrl;
            } catch (\Exception $e) {
                \Log::warning('Sidebar URL resolution failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
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
