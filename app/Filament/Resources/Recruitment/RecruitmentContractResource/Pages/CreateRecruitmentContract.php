<?php

namespace App\Filament\Resources\Recruitment\RecruitmentContractResource\Pages;

use App\Filament\Resources\Recruitment\RecruitmentContractResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRecruitmentContract extends CreateRecord
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
        
        if (str_contains($path, '/admin/') && !str_contains($path, '/public/admin/')) {
            if (str_starts_with($path, '/public/')) {
                $path = substr($path, 7);
            }
            $newPath = str_replace('/admin/', '/public/admin/', $path);
            
            $scheme = $parsed['scheme'] ?? 'https';
            $host = $parsed['host'] ?? '';
            $query = isset($parsed['query']) ? '?' . $parsed['query'] : '';
            $fragment = isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';
            
            return $scheme . '://' . $host . $newPath . $query . $fragment;
        }
        
        return $url;
    }
}
