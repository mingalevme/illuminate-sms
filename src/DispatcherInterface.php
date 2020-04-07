<?php

declare(strict_types=1);

namespace Mingalevme\Illuminate\Sms;

interface DispatcherInterface
{
    public function send(Message $message): void;

    public function sendNow(Message $message): void ;
}
