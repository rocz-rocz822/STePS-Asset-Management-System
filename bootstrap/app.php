<?php

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => \App\Http\Middleware\EnsureUserIsAdmin::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (QueryException $e, $request) {
            // MySQL foreign key restriction error code
            if ($e->getCode() === '23000' && str_contains($e->getMessage(), 'a foreign key constraint fails')) {
                $message = 'This record cannot be deleted because other records still reference it. Remove or reassign those first.';

                if ($request->expectsJson()) {
                    return response()->json(['message' => $message], 409);
                }

                return back()->with('error', $message);
            }

            return null; // let Laravel handle everything else normally
        });
    })->create();