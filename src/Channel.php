<?php

declare(strict_types=1);

namespace Mingalevme\Illuminate\Sms;

use Illuminate\Contracts\Bus\Dispatcher as BusDispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\NotificationSender;
use Mingalevme\Illuminate\Sms\Contracts\Transport;
use Mingalevme\Illuminate\Sms\Jobs\SendSmsMessage;

/**
 * @see NotificationSender
 */
class Channel
{
    /** @var string */
    protected $name;

    /** @var Transport */
    protected $transport;

    public function __construct(string $name, Transport $transport)
    {
        $this->name = $name;
        $this->transport = $transport;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTransport(): Transport
    {
        return $this->transport;
    }

    public function send(Message $message): void
    {
        if ($message instanceof ShouldQueue === false) {
            $this->sendNow($message);
            return;
        }

        /** @var BusDispatcher $bus */
        $bus = app(BusDispatcher::class);

        $bus->dispatch(
            (new SendSmsMessage($this, $message))
                ->onConnection($message->connection)
                ->onQueue($message->queue)
                ->delay($message->delay)
        );
    }

    public function sendNow(Message $message): void
    {
        SendSmsMessage::dispatchNow($this, $message);
    }

    public function sendAfterResponse(Message $message): void
    {
        SendSmsMessage::dispatchAfterResponse($this, $message);
    }
}
