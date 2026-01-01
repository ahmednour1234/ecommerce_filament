<?php

namespace App\Console\Commands;

use App\Models\MainCore\Language;
use App\Models\MainCore\Translation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class TranslationsAuditCommand extends Command
{
    protected $signature = 'translations:audit 
                            {--insert-missing : Automatically insert missing keys into database}
                            {--group= : Filter by translation group}
                            {--format=table : Output format (table, json, csv)}';

    protected $description = 'Audit translation keys in the codebase and identify missing translations';

    protected array $foundKeys = [];
    protected array $missingKeys = [];
    protected array $hardcodedStrings = [];

    public function handle(): int
    {
        $this->info('🔍 Scanning codebase for translation keys...');
        $this->newLine();

        // Scan PHP files
        $this->scanDirectory(app_path());
        $this->scanDirectory(resource_path('views'));

        // Get existing translations from DB
        $existingKeys = $this->getExistingKeys();

        // Analyze found keys
        $this->analyzeKeys($existingKeys);

        // Display results
        $this->displayResults();

        // Insert missing keys if requested
        if ($this->option('insert-missing')) {
            $this->insertMissingKeys();
        }

        return Command::SUCCESS;
    }

    protected function scanDirectory(string $path): void
    {
        $files = File::allFiles($path);
        
        foreach ($files as $file) {
            if ($file->getExtension() === 'php' || $file->getExtension() === 'blade.php') {
                $this->scanFile($file->getPathname());
            }
        }
    }

    protected function scanFile(string $filePath): void
    {
        $content = File::get($filePath);
        
        // Find tr() calls
        $this->extractTranslationKeys($content, 'tr\(', $filePath);
        
        // Find trans_dash() calls
        $this->extractTranslationKeys($content, 'trans_dash\(', $filePath);
        
        // Find __() calls (Laravel translation)
        $this->extractTranslationKeys($content, '__\(', $filePath);
        
        // Find @lang() in Blade
        $this->extractTranslationKeys($content, '@lang\(', $filePath);
        
        // Find hardcoded strings in Filament methods
        $this->findHardcodedStrings($content, $filePath);
    }

    protected function extractTranslationKeys(string $content, string $pattern, string $filePath): void
    {
        // Match function calls with string arguments
        preg_match_all('/' . preg_quote($pattern, '/') . '\s*[\'"]([^\'"]+)[\'"]/', $content, $matches);
        
        if (!empty($matches[1])) {
            foreach ($matches[1] as $key) {
                if (!empty($key) && !Str::startsWith($key, '$')) {
                    $this->foundKeys[] = [
                        'key' => $key,
                        'file' => $filePath,
                        'pattern' => $pattern,
                    ];
                }
            }
        }
    }

    protected function findHardcodedStrings(string $content, string $filePath): void
    {
        // Find ->label('...'), ->title('...'), ->heading('...'), ->placeholder('...')
        $patterns = [
            '/->label\([\'"]([^\'"]+)[\'"]\)/',
            '/->title\([\'"]([^\'"]+)[\'"]\)/',
            '/->heading\([\'"]([^\'"]+)[\'"]\)/',
            '/->placeholder\([\'"]([^\'"]+)[\'"]\)/',
            '/->helperText\([\'"]([^\'"]+)[\'"]\)/',
            '/->hint\([\'"]([^\'"]+)[\'"]\)/',
            '/navigationGroup\s*=\s*[\'"]([^\'"]+)[\'"]/',
            '/navigationLabel\s*=\s*[\'"]([^\'"]+)[\'"]/',
        ];

        foreach ($patterns as $pattern) {
            preg_match_all($pattern, $content, $matches);
            if (!empty($matches[1])) {
                foreach ($matches[1] as $string) {
                    // Skip if it's a variable, function call, or very short
                    if (Str::length($string) > 2 && 
                        !Str::startsWith($string, '$') && 
                        !Str::contains($string, '(') &&
                        !Str::contains($string, '::') &&
                        !in_array(strtolower($string), ['true', 'false', 'null'])) {
                        $this->hardcodedStrings[] = [
                            'string' => $string,
                            'file' => $filePath,
                        ];
                    }
                }
            }
        }
    }

    protected function getExistingKeys(): array
    {
        $query = Translation::query();
        
        if ($group = $this->option('group')) {
            $query->where('group', $group);
        }
        
        return $query->pluck('key')->unique()->toArray();
    }

    protected function analyzeKeys(array $existingKeys): void
    {
        $uniqueKeys = collect($this->foundKeys)->pluck('key')->unique();
        
        foreach ($uniqueKeys as $key) {
            if (!in_array($key, $existingKeys)) {
                $this->missingKeys[] = $key;
            }
        }
    }

    protected function displayResults(): void
    {
        $format = $this->option('format');
        
        if ($format === 'json') {
            $this->displayJson();
        } elseif ($format === 'csv') {
            $this->displayCsv();
        } else {
            $this->displayTable();
        }
    }

    protected function displayTable(): void
    {
        $this->info('═══════════════════════════════════════════════════════');
        $this->info('Translation Audit Results');
        $this->info('═══════════════════════════════════════════════════════');
        $this->newLine();

        // Summary
        $this->info('Summary:');
        $this->line("  Found translation keys: " . count(array_unique(array_column($this->foundKeys, 'key'))));
        $this->line("  Missing from database: " . count($this->missingKeys));
        $this->line("  Hardcoded strings found: " . count($this->hardcodedStrings));
        $this->newLine();

        // Missing keys
        if (!empty($this->missingKeys)) {
            $this->warn('Missing Translation Keys:');
            $this->table(
                ['Key', 'Usage Count'],
                collect($this->missingKeys)->map(function ($key) {
                    $count = collect($this->foundKeys)->where('key', $key)->count();
                    return [$key, $count];
                })->toArray()
            );
            $this->newLine();
        } else {
            $this->info('✅ All translation keys exist in database!');
            $this->newLine();
        }

        // Hardcoded strings (show top 20)
        if (!empty($this->hardcodedStrings)) {
            $this->warn('Hardcoded Strings (Top 20):');
            $grouped = collect($this->hardcodedStrings)
                ->groupBy('string')
                ->map(function ($items, $string) {
                    return [
                        'string' => $string,
                        'count' => $items->count(),
                        'files' => $items->pluck('file')->unique()->take(3)->implode(', '),
                    ];
                })
                ->sortByDesc('count')
                ->take(20)
                ->values()
                ->toArray();

            $this->table(
                ['String', 'Count', 'Files'],
                collect($grouped)->map(function ($item) {
                    return [
                        Str::limit($item['string'], 50),
                        $item['count'],
                        Str::limit($item['files'], 60),
                    ];
                })->toArray()
            );
            $this->newLine();
        }
    }

    protected function displayJson(): void
    {
        $data = [
            'summary' => [
                'found_keys' => count(array_unique(array_column($this->foundKeys, 'key'))),
                'missing_keys' => count($this->missingKeys),
                'hardcoded_strings' => count($this->hardcodedStrings),
            ],
            'missing_keys' => $this->missingKeys,
            'hardcoded_strings' => collect($this->hardcodedStrings)
                ->groupBy('string')
                ->map(function ($items) {
                    return [
                        'count' => $items->count(),
                        'files' => $items->pluck('file')->unique()->toArray(),
                    ];
                })
                ->toArray(),
        ];

        $this->line(json_encode($data, JSON_PRETTY_PRINT));
    }

    protected function displayCsv(): void
    {
        $this->line('Type,Key/String,Count');
        
        foreach ($this->missingKeys as $key) {
            $count = collect($this->foundKeys)->where('key', $key)->count();
            $this->line("missing_key,\"$key\",$count");
        }
        
        foreach (collect($this->hardcodedStrings)->groupBy('string') as $string => $items) {
            $this->line("hardcoded_string,\"$string\"," . $items->count());
        }
    }

    protected function insertMissingKeys(): void
    {
        if (empty($this->missingKeys)) {
            $this->info('No missing keys to insert.');
            return;
        }

        $this->info('Inserting missing keys into database...');
        
        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->error('English or Arabic language not found in database!');
            return;
        }

        $inserted = 0;
        foreach ($this->missingKeys as $key) {
            // Determine group from key structure
            $group = $this->determineGroup($key);
            
            // Insert English (use key as default)
            Translation::firstOrCreate(
                [
                    'key' => $key,
                    'group' => $group,
                    'language_id' => $english->id,
                ],
                [
                    'value' => $key, // Use key as placeholder
                ]
            );

            // Insert Arabic (use key as default)
            Translation::firstOrCreate(
                [
                    'key' => $key,
                    'group' => $group,
                    'language_id' => $arabic->id,
                ],
                [
                    'value' => $key, // Use key as placeholder
                ]
            );

            $inserted++;
        }

        $this->info("✅ Inserted $inserted missing keys into database.");
    }

    protected function determineGroup(string $key): string
    {
        // Determine group from key prefix
        if (Str::startsWith($key, 'sidebar.')) {
            return 'dashboard';
        } elseif (Str::startsWith($key, 'menu.')) {
            return 'menu';
        } elseif (Str::startsWith($key, 'pages.')) {
            return 'dashboard';
        } elseif (Str::startsWith($key, 'actions.')) {
            return 'dashboard';
        } elseif (Str::startsWith($key, 'forms.')) {
            return 'dashboard';
        } elseif (Str::startsWith($key, 'tables.')) {
            return 'dashboard';
        } elseif (Str::startsWith($key, 'reports.')) {
            return 'dashboard';
        } elseif (Str::startsWith($key, 'common.')) {
            return 'dashboard';
        } elseif (Str::startsWith($key, 'widgets.')) {
            return 'dashboard';
        }

        return 'dashboard'; // Default
    }
}

