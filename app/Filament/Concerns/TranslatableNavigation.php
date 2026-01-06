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
        // Get default label - check for navigationLabel, then title, then modelLabel (if exists), then class name
        $defaultLabel = static::$navigationLabel 
            ?? (property_exists(static::class, 'title') ? static::$title : null)
            ?? (method_exists(static::class, 'getModelLabel') ? static::getModelLabel() : null)
            ?? class_basename(static::class);
        
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
        // Only try to get model if the method exists (for Resources)
        $itemName = null;
        if (method_exists(static::class, 'getModel')) {
            try {
                $model = static::getModel();
                $itemName = strtolower(class_basename($model));
            } catch (\Exception $e) {
                // If getModel fails, use class name
                $itemName = strtolower(class_basename(static::class));
            }
        } else {
            // For Pages, use class name
            $itemName = strtolower(class_basename(static::class));
            // Remove 'Page' suffix if present
            $itemName = str_replace('page', '', $itemName);
        }
        
        // Normalize group name: remove special characters, replace spaces and & with underscore
        $group = '';
        if (static::$navigationGroup) {
            $group = strtolower(static::$navigationGroup);
            $group = str_replace([' & ', ' &', '& ', '&'], '_', $group);
            $group = str_replace([' ', '-', '/'], '_', $group);
            $group = preg_replace('/[^a-z0-9_]/', '', $group);
            $group = preg_replace('/_+/', '_', $group); // Remove multiple underscores
            $group = trim($group, '_'); // Remove leading/trailing underscores
        }
        
        // Build sidebar key: sidebar.{group}.{item}
        if ($group) {
            $translationKey = "sidebar.{$group}.{$itemName}";
        } else {
            $translationKey = "sidebar.{$itemName}";
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
        // Normalize: remove special characters, replace spaces and & with underscore
        $groupKey = strtolower($group);
        $groupKey = str_replace([' & ', ' &', '& ', '&'], '_', $groupKey);
        $groupKey = str_replace([' ', '-', '/'], '_', $groupKey);
        $groupKey = preg_replace('/[^a-z0-9_]/', '', $groupKey);
        $groupKey = preg_replace('/_+/', '_', $groupKey); // Remove multiple underscores
        $groupKey = trim($groupKey, '_'); // Remove leading/trailing underscores
        
        $translationKey = "sidebar.{$groupKey}";
        
        return tr($translationKey, [], null, 'dashboard') ?: $group;
    }
}

