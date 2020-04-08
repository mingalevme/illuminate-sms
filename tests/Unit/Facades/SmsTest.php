<?php

declare(strict_types=1);

use Mingalevme\Illuminate\Sms\Facades\Sms;
use Mingalevme\Illuminate\Sms\Manager;
use Mingalevme\Tests\Illuminate\Sms\TestCase;

class SmsTest extends TestCase
{
    public function test(): void
    {
        $this->assertInstanceOf(Manager::class, Sms::getFacadeRoot());
    }
}
