<?php

namespace App\Filament\Resources\Recruitment\RecruitmentContractResource\Pages;

use App\Filament\Resources\Recruitment\RecruitmentContractResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewRecruitmentContract extends ViewRecord
{
    protected static string $resource = RecruitmentContractResource::class;

    public static function getUrl(array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?\Illuminate\Database\Eloquent\Model $tenant = null): string
    {
        $url = parent::getUrl($parameters, $isAbsolute, $panel, $tenant);
        return static::addPublicToUrl($url);
    }

    protected static function addPublicToUrl(string $url): string
    {
        $parsed = parse_url($url);
        $path = $parsed['path'] ?? '';
        
        if (str_starts_with($path, '/public/')) {
            $path = substr($path, 7);
        }
        
        if (str_contains($path, '/admin/') && !str_contains($path, '/admin/public/')) {
            $newPath = str_replace('/admin/', '/admin/public/', $path);
            
            $scheme = $parsed['scheme'] ?? 'https';
            $host = $parsed['host'] ?? '';
            $query = isset($parsed['query']) ? '?' . $parsed['query'] : '';
            $fragment = isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';
            
            return $scheme . '://' . $host . $newPath . $query . $fragment;
        }
        
        return $url;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
