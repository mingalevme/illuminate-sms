<?php

declare(strict_types=1);

namespace Mingalevme\Illuminate\Sms\Transports;

use Mingalevme\Illuminate\Sms\Contracts\Transport;
use Mingalevme\Illuminate\Sms\Message;

class ArrayTransport implements Transport
{
    /** @var Message[]|array  */
    protected $messages = [];

    public function send(Message $message): bool
    {
        $this->messages[] = $message;
        return true;
    }

    /**
     * @return Message[]|array
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}
