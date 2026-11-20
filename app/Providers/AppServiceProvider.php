<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (
            ! $this->app->runningInConsole()
            && $this->app->has('request')
            && $this->app['request']->header('X-Forwarded-Proto') === 'https'
        ) {
            URL::forceScheme('https');
        }

        if (app()->environment('local')) {
            DB::listen(function ($query) {
                logger()->debug('DATABASE QUERY', [
                    'url' => request()->fullUrl(),
                    'method' => request()->method(),
                    'sql' => $query->sql,
                    'time_ms' => $query->time,
                ]);
            });
        }
    }
}
