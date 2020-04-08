<?php

declare(strict_types=1);

namespace Mingalevme\Illuminate\Sms;

use Closure;
use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Log\LogManager;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Mingalevme\Illuminate\Sms\Contracts\Dispatcher;
use Mingalevme\Illuminate\Sms\Contracts\Factory;
use Mingalevme\Illuminate\Sms\Contracts\Transport;
use Mingalevme\Illuminate\Sms\Transports\ArrayTransport;
use Mingalevme\Illuminate\Sms\Transports\LogTransport;
use Mingalevme\Illuminate\Sms\Transports\NullTransport;
use Mingalevme\Illuminate\Sms\Transports\SmscTransport;
use Psr\Log\LoggerInterface;

/**
 * @see CacheManager
 */
class Manager implements Dispatcher, Factory
{
    /** @var Container */
    protected $container;

    /** @var ConfigRepository */
    protected $config;

    /** @var array */
    protected $channels = [];

    /** @var array */
    protected $customCreators = [];

    public function __construct(Container $container)
    {
        $this->container = $container;
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->config = $container->make('config');
    }

    public function channel(string $name = null): Channel
    {
        $name = $name ?: $this->getDefaultChannelName();

        if (is_null($name)) {
            throw new InvalidArgumentException(sprintf(
                'Unable to resolve NULL channel for [%s].', static::class
            ));
        }

        if (empty($this->channels[$name])) {
            $this->channels[$name] = $this->resolve($name);
        }

        return $this->channels[$name];
    }

    protected function resolve(string $name): Channel
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Sms channel [{$name}] is not defined.");
        }

        if (isset($this->customCreators[$config['transport']])) {
            return $this->callCustomCreator($name, $config);
        } else {
            /**
             * @see createArrayTransport()
             * @see createNullTransport()
             * @see createSmscTransport()
             */
            $transportCreationMethodName = 'create'.Str::studly($config['transport']).'Transport';

            if (method_exists($this, $transportCreationMethodName)) {
                return $this->{$transportCreationMethodName}($name, $config);
            } else {
                throw new InvalidArgumentException("Transport [{$config['transport']}] is not supported.");
            }
        }
    }

    protected function getConfig(string $name): array
    {
        return $this->config->get("sms.channels.{$name}");
    }

    protected function createChannel(string $name, Transport $transport): Channel
    {
        return new Channel($name, $transport);
    }

    protected function createArrayTransport(string $name, array $config): Channel
    {
        return $this->createChannel($name, new ArrayTransport());
    }

    protected function createLogTransport(string $name, array $config): Channel
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $logger = $this->container->make(LoggerInterface::class);

        if ($logger instanceof LogManager) {
            $logger = $logger->channel($config['channel'] ?? null);
        }

        return $this->createChannel($name, new LogTransport($logger));
    }

    protected function createNullTransport(string $name, array $config): Channel
    {
        return $this->createChannel($name, new NullTransport());
    }

    protected function createSmscTransport(string $name, array $config): Channel
    {
        return $this->createChannel($name, new SmscTransport($config['login'], $config['password'], $config));
    }

    protected function callCustomCreator(string $name, array $config)
    {
        return $this->customCreators[$config['transport']]($this->container, $name, $config);
    }

    public function extend(string $transport, Closure $callback): self
    {
        $this->customCreators[$transport] = $callback;

        return $this;
    }

    public function getDefaultChannelName(): string
    {
        return $this->config->get('sms.default');
    }

    public function setDefaultChannelName(string $name): void
    {
        $this->config->set('sms.default', $name);
    }

    public function __call(string $method, array $parameters)
    {
        return $this->channel()->$method(...$parameters);
    }

    public function send(Message $message): void
    {
        $this->channel()->send($message);
    }

    public function sendNow(Message $message): void
    {
        $this->channel()->sendNow($message);
    }
}
