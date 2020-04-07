<?php

declare(strict_types=1);

namespace Mingalevme\Illuminate\Sms\Transports;

class NullTransport implements TransportInterface
{
    public function send(string $phone, string $message): bool
    {
        return true;
    }
}
