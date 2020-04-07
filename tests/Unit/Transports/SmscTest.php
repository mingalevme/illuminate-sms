<?php

declare(strict_types=1);

namespace Mingalevme\Tests\Illuminate\Sms\Unit\Transports;

use Illuminate\Support\Facades\Http;
use Mingalevme\Illuminate\Sms\Exception;
use Mingalevme\Illuminate\Sms\Message;
use Mingalevme\Illuminate\Sms\Transports\SmscTransport;
use Mingalevme\Tests\Illuminate\Sms\TestCase;

class SmscTest extends TestCase
{
    /** @var string */
    protected $text = 'Test message';

    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * @throws Exception
     */
    public function testRealSendingMessage(): void
    {
        $login = strval(getenv('MINGALEVME_ILLUMINATE_SMS_TESTING_SMSC_LOGIN'));
        $password = strval(getenv('MINGALEVME_ILLUMINATE_SMS_TESTING_SMSC_PASSWORD'));
        $phone = strval(getenv('MINGALEVME_ILLUMINATE_SMS_TESTING_PHONE'));

        if (empty($login) || empty($password) || empty($phone)) {
            $this->markTestSkipped('To test the class set up MINGALEVME_ILLUMINATE_SMS_TESTING_* environment variables');
        }

        $smsc = new SmscTransport($login, $password);
        $this->assertTrue($smsc->send(new Message($phone, $this->text)));
    }

    /**
     * @throws Exception
     */
    public function testSendingMessage(): void
    {
        $login = 'mingalevme';
        $password = 'password';
        $phone = '+79000000000';

        //$url = 'https://smsc.ru/sys/send.php?phones=%2B79000000000&mes=Test+message&charset=utf8&fmt=3&login=mingalevme&psw=password';

        Http::fake([
            '//smsc.ru/sys/send.php*' => Http::response(['id' => 1, 'cnt' => 1], 200),
        ]);

        $smsc = new SmscTransport($login, $password);
        $this->assertTrue($smsc->send(new Message($phone, $this->text)));
    }

    public function testErrorTriggering(): void
    {
        $login = 'mingalevme';
        $password = 'password';
        $phone = '+79000000000';

        $smsc = new SmscTransport($login, $password);
        try {
            $smsc->send(new Message($phone, $this->text));
            $this->fail('Exception has not been thrown');
        } catch (Exception $e) {
            $this->assertSame($e->getContext()['transport'], SmscTransport::class);
            $this->assertSame($e->getContext()['login'], $login);
            $this->assertSame($e->getContext()['phone'], $phone);
            $this->assertSame($e->getContext()['text'], $this->text);
        }
    }
}
