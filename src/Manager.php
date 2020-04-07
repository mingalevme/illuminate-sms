<?php

declare(strict_types=1);

namespace Mingalevme\Illuminate\Sms;

use Mingalevme\Illuminate\Sms\Transports\ArrayTransport;
use Mingalevme\Illuminate\Sms\Transports\NullTransport;
use Mingalevme\Illuminate\Sms\Transports\SmscTransport;
use Closure;
use Illuminate\Cache\CacheManager;
use Illuminate\Contracts\Foundation\Application;
use InvalidArgumentException;

/**
 * @see CacheManager
 */
class Manager implements DispatcherInterface
{
    /** @var Application */
    protected $app;

    /** @var array */
    protected $channels = [];

    /** @var array */
    protected $customCreators = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function channel(string $name = null): Channel
    {
        $name = $name ?: $this->getDefaultChannelName();

        return $this->get($name);
    }

    protected function get(string $name): Channel
    {
        return $this->channels[$name] ?? $this->resolve($name);
    }

    protected function getConfig($name): array
    {
        return $this->app['config']["sms.channel.{$name}"];
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
            $transportCreationMethodName = 'create'.ucfirst($config['transport']).'Transport';

            if (method_exists($this, $transportCreationMethodName)) {
                return $this->{$transportCreationMethodName}($name, $config);
            } else {
                throw new InvalidArgumentException("Transport [{$config['transport']}] is not supported.");
            }
        }
    }

    protected function createArrayTransport(string $name, array $config): Channel
    {
        return new Channel($name, new ArrayTransport());
    }

    protected function createNullTransport(string $name, array $config): Channel
    {
        return new Channel($name, new NullTransport());
    }

    protected function createSmscTransport(string $name, array $config): Channel
    {
        return new Channel($name, new SmscTransport($config));
    }

    protected function callCustomCreator(string $name, array $config)
    {
        return $this->customCreators[$config['transport']]($this->app, $name, $config);
    }

    public function extend(string $transport, Closure $callback): self
    {
        $this->customCreators[$transport] = $callback->bindTo($this, $this);

        return $this;
    }

    public function getDefaultChannelName(): string
    {
        return $this->app['config']['sms.default'];
    }

    public function setDefaultChannelName(string $name): void
    {
        $this->app['config']['sms.default'] = $name;
    }

    public function __call(string $method, array $parameters)
    {
        return $this->channel()->$method(...$parameters);
    }

    /*
     * DispatcherInterface
     */

    public function send(Message $message): void
    {
        $this->channel()->send($message);
    }

    public function sendNow(Message $message): void
    {
        $this->channel()->sendNow($message);
    }
}
