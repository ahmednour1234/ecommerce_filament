<?php

namespace App\Services\MainCore;

use App\Models\User;
use App\Models\MainCore\Language;
use App\Models\MainCore\UserPreference;
use Illuminate\Support\Facades\Auth;

class LocaleService
{
    public function currentUser(): ?User
    {
        return Auth::user();
    }

    public function userPreferences(?User $user = null): ?UserPreference
    {
        $user ??= $this->currentUser();
        if (!$user) {
            return null;
        }

        return $user->preferences()->first(); // اعمل علاقة preferences في User
    }

    public function currentLanguageCode(): string
    {
        $pref = $this->userPreferences();

        if ($pref && $pref->language) {
            return $pref->language->code;
        }

        $lang = Language::where('is_default', true)->first();

        return $lang?->code ?? config('app.locale', 'ar');
    }

    public function currentTimezone(): string
    {
        $pref = $this->userPreferences();

        if ($pref && $pref->timezone) {
            return $pref->timezone;
        }

        return config('app.timezone', 'UTC');
    }
}
