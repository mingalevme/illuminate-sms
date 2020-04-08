<?php

declare(strict_types=1);

namespace Mingalevme\Illuminate\Sms\Facades;

use Closure;
use Illuminate\Mail\Transport\Transport;
use Illuminate\Support\Facades\Facade;
use Mingalevme\Illuminate\Sms\Channel;
use Mingalevme\Illuminate\Sms\Manager;
use Mingalevme\Illuminate\Sms\Message;

/**
 * @method static Channel channel(string|null $name = null)
 * @see Manager::channel()
 *
 * @method static string getDefaultChannelName()
 * @see Manager::getDefaultChannelName()
 *
 * @method static string setDefaultChannelName(string $name)
 * @see Manager::setDefaultChannelName()
 *
 * @method static bool send(Message $message)
 * @see Manager::send()
 *
 * @method static bool sendNow(Message $message)
 * @see Manager::sendNow()
 *
 * @method static Manager extend(string $transport, Closure $callback)
 * @see Manager::extend()
 *
 * @method static Transport getTransport()
 * @see Channel::getTransport()
 *
 * @see Manager
 */
class Sms extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Manager::class;
    }
}
