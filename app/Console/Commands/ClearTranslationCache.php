<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ClearTranslationCache extends Command
{
    protected $signature = 'translation:clear-cache';
    protected $description = 'Clear translation cache only';

    public function handle()
    {
        $this->info('Clearing translation cache...');
        
        // Clear all translation cache
        $keys = Cache::getRedis()->keys('*translation*');
        if (!empty($keys)) {
            foreach ($keys as $key) {
                Cache::forget(str_replace(config('cache.prefix') . ':', '', $key));
            }
        }
        
        // Also try to clear by pattern
        Cache::flush();
        
        $this->info('✓ Translation cache cleared successfully!');
        $this->info('Please refresh your browser to see the changes.');
        
        return 0;
    }
}
