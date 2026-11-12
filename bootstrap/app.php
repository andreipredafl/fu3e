<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // The CSV export needs cell values exactly as typed, so they must not
        // be trimmed or turned from empty strings into null on that route
        $isCsvExport = fn(Request $request) => $request->is('api/csv-export');

        $middleware->trimStrings(except: [$isCsvExport]);
        $middleware->convertEmptyStringsToNull(except: [$isCsvExport]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
