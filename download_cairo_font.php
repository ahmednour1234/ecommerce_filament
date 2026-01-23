<?php
/**
 * Helper script to download Cairo font for Arabic PDF support
 * Run: php download_cairo_font.php
 */

$fontsDir = __DIR__ . '/public/fonts';
if (!is_dir($fontsDir)) {
    mkdir($fontsDir, 0755, true);
}

$cairoUrls = [
    'Regular' => 'https://github.com/google/fonts/raw/main/ofl/cairo/Cairo-Regular.ttf',
    'Bold' => 'https://github.com/google/fonts/raw/main/ofl/cairo/Cairo-Bold.ttf',
];

echo "Downloading Cairo fonts for Arabic PDF support...\n\n";

foreach ($cairoUrls as $weight => $url) {
    $filename = "Cairo-{$weight}.ttf";
    $filepath = "{$fontsDir}/{$filename}";
    
    if (file_exists($filepath)) {
        echo "✓ {$filename} already exists, skipping...\n";
        continue;
    }
    
    echo "Downloading {$filename}...\n";
    
    $ch = curl_init($url);
    $fp = fopen($filepath, 'wb');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    fclose($fp);
    
    if ($httpCode === 200 && file_exists($filepath) && filesize($filepath) > 0) {
        echo "✓ {$filename} downloaded successfully!\n";
    } else {
        echo "✗ Failed to download {$filename}. Please download manually from:\n";
        echo "  https://fonts.google.com/specimen/Cairo\n";
        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }
}

echo "\nDone! Fonts are ready in: {$fontsDir}\n";
echo "You can now test Arabic PDF export at: /admin/exports/test-arabic-pdf\n";
