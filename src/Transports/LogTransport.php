<?php

declare(strict_types=1);

namespace Mingalevme\Illuminate\Sms\Transports;

use Mingalevme\Illuminate\Sms\Contracts\Transport;
use Mingalevme\Illuminate\Sms\Message;
use Psr\Log\LoggerInterface;

class LogTransport implements Transport
{
    /** @var LoggerInterface */
    protected $logger;

    /**
     * Create a new log transport instance.
     *
     * @param  LoggerInterface  $logger
     * @return void
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function send(Message $message): bool
    {
        $this->logger->debug($this->format($message));
    }

    protected function format(Message $message): string
    {
        return sprintf('Sms (%s): %s', $message->getPhone(), $message->getText());
    }
}
