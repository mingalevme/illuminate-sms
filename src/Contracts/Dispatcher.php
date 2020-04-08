<?php

declare(strict_types=1);

namespace Mingalevme\Illuminate\Sms\Contracts;

use Mingalevme\Illuminate\Sms\Message;

interface Dispatcher
{
    public function send(Message $message): void;

    public function sendNow(Message $message): void;
}
