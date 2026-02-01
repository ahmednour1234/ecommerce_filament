<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // خليه فاضي دلوقتي، هنسيب الإعدادات الافتراضية للّارافيل
        // لو احتجنا نضيف middlewares بعدين نضيفهم هنا واحدة واحدة
        $middleware->alias([
            'max_request_size' => \App\Http\Middleware\MaxRequestSizeMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();

// Manually set the namespace if detection fails
try {
    $app->getNamespace();
} catch (\RuntimeException $e) {
    // Use reflection to set the namespace property
    $reflection = new \ReflectionClass($app);
    $property = $reflection->getProperty('namespace');
    $property->setAccessible(true);
    $property->setValue($app, 'App');
}

return $app;
