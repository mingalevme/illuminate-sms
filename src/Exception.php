<?php

declare(strict_types=1);

namespace Mingalevme\Illuminate\Sms;

use Throwable;

class Exception extends \Exception
{
    protected $context;

    public function __construct($message = "", $code = 0, array $context = [], Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }

    public function getContext(): array
    {
        return (array) $this->context;
    }
}
