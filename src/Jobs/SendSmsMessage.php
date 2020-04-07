<?php

declare(strict_types=1);

namespace Mingalevme\Illuminate\Sms\Jobs;

use Mingalevme\Illuminate\Sms\Channel;
use Mingalevme\Illuminate\Sms\Events\SmsSending;
use Mingalevme\Illuminate\Sms\Events\SmsSent;
use Mingalevme\Illuminate\Sms\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class SendSmsMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /** @var Channel */
    protected $channel;

    /** @var Message */
    protected $message;

    /** @var EventDispatcher */
    protected $events;

    public function __construct(Channel $channel, Message $message)
    {
        $this->channel = $channel;
        $this->message = $message;
        $this->events = app('events');
    }

    public function handle(): void
    {
        if (!$this->shouldSendNotification($this->message)) {
            return;
        }

        $this->channel->getTransport()->send($this->message);

        $this->events->dispatch(
            new SmsSent($this->channel, $this->message)
        );
    }

    protected function shouldSendNotification(Message $message)
    {
        return $this->events->until(
                new SmsSending($this->channel, $message)
            ) !== false;
    }
}
