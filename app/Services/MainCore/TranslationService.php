<?php

namespace App\Services\MainCore;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Support\Facades\Cache;

class TranslationService
{
    /**
     * Get translation by key and language with English fallback
     */
    public function get(string $key, ?string $languageCode = null, ?string $group = 'dashboard', ?string $default = null): string
    {
        $languageCode = $languageCode ?? $this->getCurrentLanguageCode();
        
        // Try to get from cache first
        $cacheKey = "translation.{$group}.{$key}.{$languageCode}";
        
        return Cache::remember($cacheKey, 3600, function () use ($key, $languageCode, $group, $default) {
            // Try current locale first
            $language = Language::where('code', $languageCode)->where('is_active', true)->first();
            
            if ($language) {
                $translation = Translation::where('key', $key)
                    ->where('group', $group)
                    ->where('language_id', $language->id)
                    ->first();
                
                if ($translation) {
                    return $translation->value;
                }
            }
            
            // Fallback to English if current locale translation not found
            if ($languageCode !== 'en') {
                $englishLanguage = Language::where('code', 'en')->where('is_active', true)->first();
                
                if ($englishLanguage) {
                    $englishTranslation = Translation::where('key', $key)
                        ->where('group', $group)
                        ->where('language_id', $englishLanguage->id)
                        ->first();
                    
                    if ($englishTranslation) {
                        return $englishTranslation->value;
                    }
                }
            }
            
            // Final fallback: default or key
            return $default ?? $key;
        });
    }

    /**
     * Get current language code from user preference or session
     */
    public function getCurrentLanguageCode(): string
    {
        // Try to get from user preference
        if (auth()->check()) {
            $preference = auth()->user()->preferences;
            if ($preference && $preference->language) {
                return $preference->language->code;
            }
        }
        
        // Try to get from session
        $sessionLang = session('locale');
        if ($sessionLang) {
            return $sessionLang;
        }
        
        // Get default language
        $defaultLanguage = Language::where('is_default', true)->where('is_active', true)->first();
        
        return $defaultLanguage?->code ?? 'ar';
    }

    /**
     * Set current language
     */
    public function setLanguage(string $languageCode): void
    {
        session(['locale' => $languageCode]);
        
        if (auth()->check()) {
            $preference = auth()->user()->preferences;
            if ($preference) {
                $language = Language::where('code', $languageCode)->first();
                if ($language) {
                    $preference->update(['language_id' => $language->id]);
                }
            }
        }
        
        // Clear translation cache
        Cache::flush();
    }

    /**
     * Get all translations for a group and language
     */
    public function getGroup(string $group, ?string $languageCode = null): array
    {
        $languageCode = $languageCode ?? $this->getCurrentLanguageCode();
        $language = Language::where('code', $languageCode)->where('is_active', true)->first();
        
        if (!$language) {
            return [];
        }
        
        return Translation::where('group', $group)
            ->where('language_id', $language->id)
            ->pluck('value', 'key')
            ->toArray();
    }

    /**
     * Clear translation cache
     */
    public function clearCache(): void
    {
        Cache::flush();
    }
}

