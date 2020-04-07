<?php

declare(strict_types=1);

namespace Mingalevme\Illuminate\Sms;

use Illuminate\Support\ServiceProvider;

class SmsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Manager::class, function ($app) {
            return new Manager($app);
        });

        $this->app->alias(Manager::class, DispatcherInterface::class);
    }

    public function provides(): array
    {
        return [
            Manager::class,
            DispatcherInterface::class,
        ];
    }
}
