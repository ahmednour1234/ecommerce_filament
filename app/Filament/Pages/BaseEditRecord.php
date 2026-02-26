<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\AddsPublicToUrl;
use Filament\Resources\Pages\EditRecord;

abstract class BaseEditRecord extends EditRecord
{
    use AddsPublicToUrl;

    protected function getRedirectUrl(): string
    {
        $url = parent::getRedirectUrl();
        return static::addPublicToUrl($url);
    }
}
