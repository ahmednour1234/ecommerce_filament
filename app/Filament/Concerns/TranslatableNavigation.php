<?php

namespace App\Filament\Concerns;

trait TranslatableNavigation
{
    /**
     * Get translated navigation label
     * Uses tr() helper with menu group
     */
    public static function getNavigationLabel(): string
    {
        $defaultLabel = static::$navigationLabel ?? static::getModelLabel();
        
        // If a custom translation key is set, use it
        if (isset(static::$navigationTranslationKey)) {
            return tr(static::$navigationTranslationKey, $defaultLabel);
        }
        
        // Try to get translation - use model name or navigation label
        $modelName = strtolower(class_basename(static::getModel()));
        $translationKey = 'menu.' . $modelName;
        
        return tr($translationKey, $defaultLabel);
    }

    /**
     * Get translated navigation group
     * Uses tr() helper with menu group
     */
    public static function getNavigationGroup(): ?string
    {
        $group = static::$navigationGroup;
        
        if (!$group) {
            return null;
        }
        
        // Translate the group name
        $groupKey = strtolower(str_replace(' ', '_', $group));
        $translationKey = 'menu.' . $groupKey;
        
        return tr($translationKey, $group);
    }
}

