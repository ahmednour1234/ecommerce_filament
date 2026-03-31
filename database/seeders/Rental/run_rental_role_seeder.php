<?php
// Standalone script - seeds Rental role & permissions without Laravel bootstrap
// Run from project root: php database/seeders/Rental/run_rental_role_seeder.php

$envFile = dirname(__DIR__, 3) . '/.env';
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
    $pdo = new PDO("mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $guardName = 'web';
    $roleName  = 'مدير قسم التأجير';

    $permissions = [
        'rental.contracts.view_any',
        'rental.contracts.view',
        'rental.contracts.create',
        'rental.contracts.update',
        'rental.contracts.delete',
        'rental.contracts.restore',
        'rental.contracts.force_delete',
        'rental.requests.view_any',
        'rental.requests.view',
        'rental.requests.manage',
        'rental.requests.convert',
        'rental.cancel_refund.view_any',
        'rental.cancel_refund.view',
        'rental.cancel_refund.manage',
        'rental.payments.view_any',
        'rental.payments.view',
        'rental.payments.create',
        'rental.payments.refund',
        'rental.print.contract',
        'rental.print.invoice',
        'rental.reports.view',
    ];

    // ─── Create permissions ───────────────────────────────────────────────
    $permissionIds = [];
    foreach ($permissions as $perm) {
        $stmt = $pdo->prepare("SELECT id FROM permissions WHERE name = ? AND guard_name = ? LIMIT 1");
        $stmt->execute([$perm, $guardName]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $permissionIds[] = $row['id'];
            echo "  [exists] {$perm}\n";
        } else {
            $now = date('Y-m-d H:i:s');
            $ins = $pdo->prepare("INSERT INTO permissions (name, guard_name, created_at, updated_at) VALUES (?, ?, ?, ?)");
            $ins->execute([$perm, $guardName, $now, $now]);
            $permissionIds[] = (int) $pdo->lastInsertId();
            echo "  [created] {$perm}\n";
        }
    }

    // ─── Create role ──────────────────────────────────────────────────────
    $stmt = $pdo->prepare("SELECT id FROM roles WHERE name = ? AND guard_name = ? LIMIT 1");
    $stmt->execute([$roleName, $guardName]);
    $roleRow = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($roleRow) {
        $roleId = $roleRow['id'];
        echo "\n[exists] Role: {$roleName} (id={$roleId})\n";
    } else {
        $now = date('Y-m-d H:i:s');
        $ins = $pdo->prepare("INSERT INTO roles (name, guard_name, created_at, updated_at) VALUES (?, ?, ?, ?)");
        $ins->execute([$roleName, $guardName, $now, $now]);
        $roleId = (int) $pdo->lastInsertId();
        echo "\n[created] Role: {$roleName} (id={$roleId})\n";
    }

    // ─── Sync role_has_permissions ────────────────────────────────────────
    $pdo->prepare("DELETE FROM role_has_permissions WHERE role_id = ?")->execute([$roleId]);
    $ins = $pdo->prepare("INSERT IGNORE INTO role_has_permissions (permission_id, role_id) VALUES (?, ?)");
    foreach ($permissionIds as $pid) {
        $ins->execute([$pid, $roleId]);
    }
    echo "[synced] " . count($permissionIds) . " permissions → role '{$roleName}'\n";

    // ─── Give permissions to super_admin role too ─────────────────────────
    $stmt = $pdo->prepare("SELECT id FROM roles WHERE name IN ('super_admin','Super Admin') AND guard_name = ? LIMIT 1");
    $stmt->execute([$guardName]);
    $superRow = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($superRow) {
        $superRoleId = $superRow['id'];
        $ins = $pdo->prepare("INSERT IGNORE INTO role_has_permissions (permission_id, role_id) VALUES (?, ?)");
        foreach ($permissionIds as $pid) {
            $ins->execute([$pid, $superRoleId]);
        }
        echo "[synced] All rental permissions → super_admin role\n";
    }

    // ─── Clear Spatie permission cache ────────────────────────────────────
    try {
        $pdo->exec("DELETE FROM cache WHERE key LIKE '%spatie.permission.cache%'");
        echo "[cleared] Spatie permission cache\n";
    } catch (\Throwable $e) {
        // cache table may use different driver, ignore
    }

    echo "\n✓ Done! Role '{$roleName}' is ready with " . count($permissionIds) . " permissions.\n";
    echo "  To assign this role to a user:\n";
    echo "  → Go to Admin → Users → Edit user → Roles → Select 'مدير قسم التأجير'\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
