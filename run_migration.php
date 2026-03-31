<?php
// Parse .env manually to avoid Laravel bootstrap
$envFile = __DIR__ . '/.env';
$env = [];
if (file_exists($envFile)) {
    foreach (file($envFile) as $line) {
        $line = trim($line);
        if ($line && !str_starts_with($line, '#') && str_contains($line, '=')) {
            [$key, $val] = explode('=', $line, 2);
            $env[trim($key)] = trim($val);
        }
    }
}

$host     = $env['DB_HOST'] ?? '127.0.0.1';
$port     = $env['DB_PORT'] ?? '3306';
$dbname   = $env['DB_DATABASE'] ?? 'laravel';
$username = $env['DB_USERNAME'] ?? 'root';
$password = $env['DB_PASSWORD'] ?? '';

try {
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$dbname}", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "ALTER TABLE recruitment_contracts MODIFY COLUMN status ENUM(
        'new',
        'foreign_embassy_approval',
        'external_office_approval',
        'contract_accepted_external_office',
        'waiting_approval',
        'contract_accepted_labor_ministry',
        'sent_to_saudi_embassy',
        'visa_issued',
        'visa_cancelled',
        'travel_permit_after_visa_issued',
        'waiting_flight_booking',
        'arrival_scheduled',
        'received',
        'return_during_warranty',
        'runaway'
    ) DEFAULT 'new'";

    $pdo->exec($sql);
    echo "Migration applied successfully. foreign_embassy_approval and visa_cancelled added to status ENUM.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
