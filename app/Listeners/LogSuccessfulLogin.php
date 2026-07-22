<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;

class LogSuccessfulLogin
{
    public function handle(Login $event): void
    {
        activity('auth')
            ->causedBy($event->user)
            ->withProperties([
                'ip_address' => request()->ip(),
                'browser' => request()->userAgent(),
            ])
            ->log('User logged in.');
    }
}