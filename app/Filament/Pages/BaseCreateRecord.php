<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\AddsPublicToUrl;
use Filament\Resources\Pages\CreateRecord;

abstract class BaseCreateRecord extends CreateRecord
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
