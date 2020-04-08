<?php

declare(strict_types=1);

namespace Mingalevme\Tests\Illuminate\Sms\Unit;

use Mingalevme\Illuminate\Sms\Manager;
use Mingalevme\Illuminate\Sms\Message;
use Mingalevme\Illuminate\Sms\Transports\ArrayTransport;
use Mingalevme\Illuminate\Sms\Transports\LogTransport;
use Mingalevme\Illuminate\Sms\Transports\NullTransport;
use Mingalevme\Tests\Illuminate\Sms\TestCase;

class ManagerTest extends TestCase
{
    protected function getManager(): Manager
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        return $this->app->make(Manager::class);
    }

    public function testDeveloperCanSendMessageViaDefaultChannel(): void
    {
        $manager = $this->getManager();
        $manager->setDefaultChannelName('array');
        $manager->send(new Message('+70000000000', self::class));
        /** @var ArrayTransport $transport */
        $transport = $manager->channel()->getTransport();
        $this->assertCount(1, $transport->getMessages());
    }

    public function testDeveloperCanChangeDefaultChannel(): void
    {
        $manager = $this->getManager();

        $manager->setDefaultChannelName('null');
        $this->assertSame('null', $manager->channel()->getName());
        $this->assertInstanceOf(NullTransport::class, $manager->channel()->getTransport());

        $manager->setDefaultChannelName('array');
        $this->assertSame('array', $manager->channel()->getName());
        $this->assertInstanceOf(ArrayTransport::class, $manager->channel()->getTransport());

        $manager->setDefaultChannelName('log');
        $this->assertSame('log', $manager->channel()->getName());
        $this->assertInstanceOf(LogTransport::class, $manager->channel()->getTransport());
    }

    public function testDeveloperCanAccessChannelByName(): void
    {
        $manager = $this->getManager();
        $this->assertSame('log', $manager->channel('log')->getName());
        $this->assertInstanceOf(LogTransport::class, $manager->channel('log')->getTransport());
    }
}
