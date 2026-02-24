<?php

namespace App\Filament\Pages;

use App\Services\MainCore\TranslationService;

trait TranslatablePage
{
    /**
     * Get translated navigation label
     */
    public static function getNavigationLabel(): string
    {
        // If navigationLabel is explicitly set, use it directly (highest priority)
        if (isset(static::$navigationLabel) && !empty(static::$navigationLabel)) {
            return static::$navigationLabel;
        }
        
        $translationService = app(TranslationService::class);
        $defaultLabel = static::$title ?? 'Page';
        
        // Try to get translation - use page title or navigation label
        $pageName = strtolower(str_replace([' ', '-'], '_', $defaultLabel));
        $translationKey = 'navigation.' . $pageName;
        $translated = $translationService->get($translationKey, null, 'dashboard', $defaultLabel);
        
        return $translated !== $translationKey ? $translated : $defaultLabel;
    }

    /**
     * Get navigation group
     * Returns the group name directly as it's defined in AdminPanelProvider
     */
    public static function getNavigationGroup(): ?string
    {
        return static::$navigationGroup;
    }
}

