<?php

declare(strict_types=1);

namespace Mingalevme\Illuminate\Sms;

use Illuminate\Bus\Queueable;

class Message
{
    use Queueable;

    /** @var string */
    protected $phone;

    /** @var string */
    protected $text;

    public function __construct(string $phone, string $text)
    {
        $this->phone = $phone;
        $this->text = $text;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
