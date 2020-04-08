<?php

namespace Mingalevme\Illuminate\Sms\Contracts;

use Mingalevme\Illuminate\Sms\Channel;
use Mingalevme\Illuminate\Sms\Message;

interface Factory
{
    public function channel(string $name = null): Channel;

    public function send(Message $message): void;

    public function sendNow(Message $message): void;
}
