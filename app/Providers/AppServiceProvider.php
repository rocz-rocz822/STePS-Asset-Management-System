<?php

namespace App\Providers;

use App\Models\Asset;
use App\Observers\AssetObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use App\Listeners\LogSuccessfulLogin;
use App\Listeners\LogSuccessfulLogout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
{
    if (config('app.env') === 'production') {
        URL::forceScheme('https');
    }

    Asset::observe(AssetObserver::class);

    Event::listen(Login::class, LogSuccessfulLogin::class);
    Event::listen(Logout::class, LogSuccessfulLogout::class);
}

}

