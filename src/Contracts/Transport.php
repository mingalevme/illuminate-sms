<?php

namespace Mingalevme\Illuminate\Sms\Contracts;

use Mingalevme\Illuminate\Sms\Message;

interface Transport
{
    public function send(Message $message): bool;
}
