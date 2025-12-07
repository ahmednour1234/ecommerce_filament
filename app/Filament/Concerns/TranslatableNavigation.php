<?php

namespace App\Filament\Concerns;

use App\Services\MainCore\TranslationService;

trait TranslatableNavigation
{
    /**
     * Get translated navigation label
     */
    public static function getNavigationLabel(): string
    {
        $translationService = app(TranslationService::class);
        $defaultLabel = static::$navigationLabel ?? static::getModelLabel();
        
        // Try to get translation - use model name or navigation label
        $modelName = strtolower(class_basename(static::getModel()));
        $translationKey = 'navigation.' . $modelName;
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

