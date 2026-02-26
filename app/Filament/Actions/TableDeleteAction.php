<?php

namespace App\Filament\Actions;

use Filament\Tables\Actions\DeleteAction as BaseDeleteAction;

class TableDeleteAction extends BaseDeleteAction
{
    public function getUrl(): ?string
    {
        $url = parent::getUrl();
        
        if ($url === null) {
            return null;
        }
        
        return $this->addPublicToUrl($url);
    }

    public function successRedirectUrl(\Closure|string|null $url): static
    {
        if ($url !== null && is_string($url)) {
            $url = $this->addPublicToUrl($url);
        }
        
        return parent::successRedirectUrl($url);
    }

    protected function addPublicToUrl(string $url): string
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
