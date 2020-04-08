<?php

declare(strict_types=1);

namespace Mingalevme\Illuminate\Sms;

use Illuminate\Support\ServiceProvider;
use Mingalevme\Illuminate\Sms\Contracts\Dispatcher;
use Mingalevme\Illuminate\Sms\Contracts\Factory;

class SmsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Manager::class, function ($app) {
            return new Manager($app);
        });

        $this->app->alias(Manager::class, Dispatcher::class);

        $this->app->alias(Manager::class, Factory::class);
    }

    public function provides(): array
    {
        return [
            Manager::class,
            Dispatcher::class,
            Factory::class,
        ];
    }
}
