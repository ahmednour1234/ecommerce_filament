<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;

class Login extends BaseLogin
{
    protected function getRedirectUrl(): string
    {
        $user = \Illuminate\Support\Facades\Auth::user();

        if ($user && $user->type === \App\Models\User::TYPE_COMPANY_OWNER) {
            return route('owner.dashboard');
        }

        return \App\Filament\Pages\Dashboard::getUrl();
    }
}
