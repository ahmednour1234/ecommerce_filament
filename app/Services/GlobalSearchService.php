<?php

namespace App\Services;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Illuminate\Support\Str;

class GlobalSearchService
{
    public function search(string $query): array
    {
        if (empty(trim($query))) {
            return [];
        }

        $query = strtolower(trim($query));
        $results = [];

        $panel = Filament::getCurrentPanel();
        if (!$panel) {
            return [];
        }

        $results = array_merge(
            $results,
            $this->searchResources($panel, $query),
            $this->searchPages($panel, $query),
            $this->searchNavigationItems($panel, $query)
        );

        usort($results, fn($a, $b) => $a['group'] <=> $b['group'] ?: $a['title'] <=> $b['title']);

        return $results;
    }

    protected function searchResources($panel, string $query): array
    {
        $results = [];
        
        try {
            $resources = method_exists($panel, 'getResources') 
                ? $panel->getResources() 
                : [];
        } catch (\Exception $e) {
            $resources = [];
        }

        foreach ($resources as $resourceClass) {
            if (!is_string($resourceClass) || !class_exists($resourceClass)) {
                continue;
            }

            if (!method_exists($resourceClass, 'canViewAny') || !$resourceClass::canViewAny()) {
                continue;
            }

            if (method_exists($resourceClass, 'shouldRegisterNavigation') && !$resourceClass::shouldRegisterNavigation()) {
                continue;
            }

            try {
                $label = method_exists($resourceClass, 'getNavigationLabel')
                    ? $resourceClass::getNavigationLabel()
                    : ($resourceClass::$navigationLabel ?? class_basename($resourceClass));

                $group = method_exists($resourceClass, 'getNavigationGroup')
                    ? ($resourceClass::getNavigationGroup() ?? 'Resources')
                    : ($resourceClass::$navigationGroup ?? 'Resources');

                $icon = $resourceClass::$navigationIcon ?? 'heroicon-o-document-text';

                if ($this->matchesQuery($label, $query) || $this->matchesQuery($group, $query)) {
                    $url = method_exists($resourceClass, 'getUrl')
                        ? $resourceClass::getUrl()
                        : null;

                    if ($url) {
                        $results[] = $this->formatResult(
                            title: $label,
                            subtitle: null,
                            icon: $icon,
                            url: $url,
                            group: $group ?? 'Resources'
                        );
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return $results;
    }

    protected function searchPages($panel, string $query): array
    {
        $results = [];
        
        try {
            $pages = method_exists($panel, 'getPages') 
                ? $panel->getPages() 
                : [];
        } catch (\Exception $e) {
            $pages = [];
        }

        foreach ($pages as $pageClass) {
            if (!is_string($pageClass) || !class_exists($pageClass)) {
                continue;
            }

            if (method_exists($pageClass, 'canAccess') && !$pageClass::canAccess()) {
                continue;
            }

            if (method_exists($pageClass, 'shouldRegisterNavigation') && !$pageClass::shouldRegisterNavigation()) {
                continue;
            }

            try {
                $label = method_exists($pageClass, 'getNavigationLabel')
                    ? $pageClass::getNavigationLabel()
                    : ($pageClass::$navigationLabel ?? ($pageClass::$title ?? class_basename($pageClass)));

                $group = method_exists($pageClass, 'getNavigationGroup')
                    ? ($pageClass::getNavigationGroup() ?? 'Pages')
                    : ($pageClass::$navigationGroup ?? 'Pages');

                $icon = $pageClass::$navigationIcon ?? 'heroicon-o-document';

                if ($this->matchesQuery($label, $query) || $this->matchesQuery($group, $query)) {
                    $url = method_exists($pageClass, 'getUrl')
                        ? $pageClass::getUrl()
                        : null;

                    if ($url) {
                        $results[] = $this->formatResult(
                            title: $label,
                            subtitle: null,
                            icon: $icon,
                            url: $url,
                            group: $group ?? 'Pages'
                        );
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return $results;
    }

    protected function searchNavigationItems($panel, string $query): array
    {
        $results = [];
        
        try {
            $navigationItems = method_exists($panel, 'getNavigationItems') 
                ? $panel->getNavigationItems() 
                : [];
        } catch (\Exception $e) {
            $navigationItems = [];
        }

        foreach ($navigationItems as $item) {
            if (!($item instanceof NavigationItem)) {
                continue;
            }

            try {
                $label = is_callable($item->getLabel()) ? ($item->getLabel())() : $item->getLabel();
                $group = is_callable($item->getGroup()) ? ($item->getGroup())() : $item->getGroup();
                $icon = $item->getIcon();
                $url = is_callable($item->getUrl()) ? ($item->getUrl())() : $item->getUrl();

                if (!$url) {
                    continue;
                }

                if (method_exists($item, 'isVisible') && !$item->isVisible()) {
                    continue;
                }

                if ($this->matchesQuery($label, $query) || $this->matchesQuery($group ?? '', $query)) {
                    $results[] = $this->formatResult(
                        title: $label,
                        subtitle: null,
                        icon: $icon,
                        url: $url,
                        group: $group ?? 'Navigation'
                    );
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return $results;
    }

    protected function formatResult(string $title, ?string $subtitle, ?string $icon, string $url, string $group): array
    {
        return [
            'title' => $title,
            'subtitle' => $subtitle,
            'icon' => $icon,
            'url' => $url,
            'group' => $group,
        ];
    }

    protected function matchesQuery(string $text, string $query): bool
    {
        return Str::contains(strtolower($text), $query);
    }
}
