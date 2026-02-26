<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\AddsPublicToUrl;
use Filament\Resources\Pages\EditRecord;

abstract class BaseEditRecord extends EditRecord
{
    use AddsPublicToUrl;
}
