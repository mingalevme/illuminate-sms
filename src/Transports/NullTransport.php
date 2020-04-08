<?php

declare(strict_types=1);

namespace Mingalevme\Illuminate\Sms\Transports;

use Mingalevme\Illuminate\Sms\Contracts\Transport;
use Mingalevme\Illuminate\Sms\Message;

class NullTransport implements Transport
{
    public function send(Message $message): bool
    {
        return true;
    }
}
