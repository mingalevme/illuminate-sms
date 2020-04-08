<?php

declare(strict_types=1);

namespace Mingalevme\Tests\Illuminate\Sms;

use Illuminate\Bus\BusServiceProvider;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Illuminate\Contracts\Http\Kernel as HttpKernelContract;
use Illuminate\Events\EventServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Foundation\Testing\TestCase as IlluminateTestCase;

class TestCase extends IlluminateTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        /**
         * @noinspection PhpUnhandledExceptionInspection
         * @var ConfigRepository $config
         */
        $config = $this->app->make('config');
        $config->set('logging.channels.syslog', [
            'driver' => 'syslog',
        ]);
    }

    public function createApplication(): Application
    {
        $app = new Application(
            realpath(__DIR__.'/app/')
        );

        (new BusServiceProvider($app))->register();
        (new EventServiceProvider($app))->register();

        $app->singleton(
            HttpKernelContract::class,
            HttpKernel::class
        );

        $app->singleton(
            ConsoleKernelContract::class,
            ConsoleKernel::class
        );

        $app->singleton(
            ExceptionHandlerContract::class,
            ExceptionHandler::class
        );

        $app->make(ConsoleKernelContract::class)->bootstrap();

        return $app;
    }
}
