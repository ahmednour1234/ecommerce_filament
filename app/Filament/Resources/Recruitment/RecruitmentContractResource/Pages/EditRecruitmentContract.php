<?php

namespace App\Filament\Resources\Recruitment\RecruitmentContractResource\Pages;

use App\Filament\Resources\Recruitment\RecruitmentContractResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRecruitmentContract extends EditRecord
{
    protected static string $resource = RecruitmentContractResource::class;

    public static function getUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?\Illuminate\Database\Eloquent\Model $tenant = null): string
    {
        $url = parent::getUrl($parameters, $isAbsolute, $panel, $tenant);
        return static::addPublicToUrl($url);
    }

    protected static function addPublicToUrl(string $url): string
    {
        if (str_contains($url, '/admin/') && !str_contains($url, '/admin/public/')) {
            return str_replace('/admin/', '/admin/public/', $url);
        }
        return $url;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
