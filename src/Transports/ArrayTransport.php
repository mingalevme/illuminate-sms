<?php

declare(strict_types=1);

namespace Mingalevme\Illuminate\Sms\Transports;

class ArrayTransport implements TransportInterface
{
    protected $messages = [];

    public function send(string $phone, string $message): bool
    {
        $this->messages[] = [$phone, $message];
        return true;
    }
}
