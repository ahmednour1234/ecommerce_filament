<?php

namespace App\Filament\Concerns;

trait TranslatableNavigation
{
    /**
     * Get translated navigation label
     * Uses tr() helper with sidebar.* keys
     */
    public static function getNavigationLabel(): string
    {
        $defaultLabel = static::$navigationLabel ?? static::getModelLabel();
        
        // If a custom translation key is set, use it
        if (isset(static::$navigationTranslationKey)) {
            // Support both sidebar.* and menu.* keys for backward compatibility
            $key = static::$navigationTranslationKey;
            if (str_starts_with($key, 'menu.')) {
                // Old menu.* key - use menu group for backward compatibility
                return tr($key, $defaultLabel, null, 'menu');
            }
            // New sidebar.* key - use dashboard group
            return tr($key, [], null, 'dashboard');
        }
        
        // Try to get translation - use model name or navigation label
        $modelName = strtolower(class_basename(static::getModel()));
        $group = static::$navigationGroup ? strtolower(str_replace(' ', '_', static::$navigationGroup)) : '';
        
        // Build sidebar key: sidebar.{group}.{item}
        if ($group) {
            $translationKey = "sidebar.{$group}.{$modelName}";
        } else {
            $translationKey = "sidebar.{$modelName}";
        }
        
        return tr($translationKey, [], null, 'dashboard') ?: $defaultLabel;
    }

    /**
     * Get translated navigation group
     * Uses tr() helper with sidebar.* keys
     */
    public static function getNavigationGroup(): ?string
    {
        $group = static::$navigationGroup;
        
        if (!$group) {
            return null;
        }
        
        // Translate the group name using sidebar.* keys
        $groupKey = strtolower(str_replace(' ', '_', $group));
        $translationKey = "sidebar.{$groupKey}";
        
        return tr($translationKey, [], null, 'dashboard') ?: $group;
    }
}

