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
        $translationService = app(TranslationService::class);
        $defaultLabel = static::$navigationLabel ?? static::getTitle();
        
        // Try to get translation - use page title or navigation label
        $pageName = strtolower(str_replace([' ', '-'], '_', static::getTitle()));
        $translationKey = 'navigation.' . $pageName;
        $translated = $translationService->get($translationKey, null, 'dashboard', $defaultLabel);
        
        return $translated !== $translationKey ? $translated : $defaultLabel;
    }

    /**
     * Get translated navigation group
     */
    public static function getNavigationGroup(): ?string
    {
        $group = static::$navigationGroup;
        
        if (!$group) {
            return null;
        }
        
        $translationService = app(TranslationService::class);
        $translationKey = 'navigation.' . strtolower(str_replace(' ', '_', $group));
        $translated = $translationService->get($translationKey, null, 'dashboard', $group);
        
        return $translated !== $translationKey ? $translated : $group;
    }
}

