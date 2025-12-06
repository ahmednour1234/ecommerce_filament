<?php

namespace App\Services\MainCore;

use App\Models\MainCore\Setting;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    protected string $cacheKey = 'maincore.settings.all';
    protected int $cacheMinutes = 60; // غيّرها لو حابب

    protected array $settings = [];

    public function __construct()
    {
        $this->load();
    }

    protected function load(): void
    {
        $this->settings = Cache::remember($this->cacheKey, now()->addMinutes($this->cacheMinutes), function () {
            return Setting::query()
                ->where('autoload', true)
                ->get()
                ->mapWithKeys(function (Setting $setting) {
                    // لو value JSON نخليه Array
                    $value = $setting->value;

                    // في الموديل عاملين cast لـ array، فممكن يطلع Array جاهز
                    return [$setting->key => $value];
                })
                ->toArray();
        });
    }

    public function clearCache(): void
    {
        Cache::forget($this->cacheKey);
        $this->load();
    }

    public function get(string $key, mixed $default = null): mixed
    {
        // دعم key dot مثل app.name
        return Arr::get($this->settings, $key, $default);
    }

    public function getGroup(string $group): array
    {
        return collect($this->settings)
            ->filter(function ($value, $key) use ($group) {
                return str_starts_with($key, $group . '.');
            })
            ->toArray();
    }

    public function set(string $key, mixed $value, ?string $group = null, string $type = 'string', bool $isPublic = true, bool $autoload = true): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            [
                'value'     => $value,
                'group'     => $group ?? explode('.', $key)[0],
                'type'      => $type,
                'is_public' => $isPublic,
                'autoload'  => $autoload,
            ],
        );

        $this->clearCache();
    }

    public function all(): array
    {
        return $this->settings;
    }
}
