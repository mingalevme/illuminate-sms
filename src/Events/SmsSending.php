<?php

declare(strict_types=1);

namespace Mingalevme\Illuminate\Sms\Events;

use Mingalevme\Illuminate\Sms\Channel;
use Mingalevme\Illuminate\Sms\Message;
use Illuminate\Bus\Queueable;

class SmsSending
{
    use Queueable;

    /** @var Message */
    public $message;

    /** @var Channel */
    public $channel;

    public function __construct(Channel $channel, Message $message)
    {
        $this->channel = $channel;
        $this->message = $message;
    }
}
