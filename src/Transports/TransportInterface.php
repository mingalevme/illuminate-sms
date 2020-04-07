<?php

namespace Mingalevme\Illuminate\Sms\Transports;

use Mingalevme\Illuminate\Sms\Message;

interface TransportInterface
{
    public function send(Message $message): bool;
}
