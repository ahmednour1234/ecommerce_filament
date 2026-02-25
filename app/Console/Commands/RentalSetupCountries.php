<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class RentalSetupCountries extends Command
{
    protected $signature = 'rental:setup-countries';

    protected $description = 'Setup rental countries and clear cache';

    public function handle(): int
    {
        $this->info('Setting up rental countries...');

        $this->call('db:seed', [
            '--class' => 'Database\\Seeders\\Rental\\RentalCountriesSeeder',
            '--force' => true,
        ]);

        Cache::forget('rental.countries');

        $this->info('✅ Rental countries setup completed and cache cleared.');

        return self::SUCCESS;
    }
}
