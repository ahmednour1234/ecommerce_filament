<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\AddsPublicToUrl;
use Filament\Resources\Pages\CreateRecord;

abstract class BaseCreateRecord extends CreateRecord
{
    use AddsPublicToUrl;
}
