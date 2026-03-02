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
        
        if ($url === null) {
            return $this->getResource()::getUrl('index');
        }
        
        return static::addPublicToUrl($url);
    }
}
