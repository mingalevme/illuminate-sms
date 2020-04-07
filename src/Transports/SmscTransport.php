<?php

declare(strict_types=1);

namespace Mingalevme\Illuminate\Sms\Transports;

use Illuminate\Http\Client\Response as HttpResponse;
use Illuminate\Support\Facades\Http;
use Mingalevme\Illuminate\Sms\Exception;
use Mingalevme\Illuminate\Sms\Message;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\NullLogger;

class SmscTransport implements TransportInterface
{
    use LoggerAwareTrait;

    const ORIGIN = 'https://smsc.ru';

    const OPTION_SHORTEN_LINKS = 'shorten-links';
    const OPTION_CHARSET = 'charset';
    const OPTION_CHARSET_UTF8 = 'utf8';
    const OPTION_CHARSET_KOI8R = 'koi8-r';
    const OPTION_CHARSET_WINDOWS1251 = 'windows-1251';
    const OPTION_SENDER_ID = 'sender-id';
    const OPTION_DEBUG = 'debug';

    protected const CMD_SEND = 'send';

    /** @var string */
    protected $login;

    /** @var string */
    protected $password;

    /** @var array */
    protected $options = [
        self::OPTION_CHARSET => self::OPTION_CHARSET_UTF8,
        self::OPTION_SHORTEN_LINKS => false,
        self::OPTION_DEBUG => false,
    ];

    public function __construct(string $login, string $password, array $options = [])
    {
        $this->login = $login;
        $this->password = $password;
        $this->options = $options + $this->options;
        $this->logger = new NullLogger();
    }

    /**
     * @param Message $message
     * @return bool
     * @throws Exception
     */
    public function send(Message $message): bool
    {
        $context = [
            'transport' => get_class($this),
            'login' => $this->login,
            'phone' => $message->getPhone(),
            'text' => $message->getText(),
        ];

        $this->logger->info('Sending sms message has been started', $context);

        $url = $this->buildUrl(self::CMD_SEND, [
            'phones' => $message->getPhone(),
            'mes' => $message->getText(),
            'charset' => $this->options[self::OPTION_CHARSET],
            'fmt' => 3,
            'sender' => $this->options[self::OPTION_SENDER_ID] ?? null,
            'tinyurl' => $this->options[self::OPTION_SHORTEN_LINKS]
                ? 1
                : null,
        ]);

        /** @var HttpResponse $response */
        $response = Http::post($url);

        $responseData = $response->json();

        if (!empty($responseData['error']) || !empty($responseData['error_code'])) {
            throw new Exception($responseData['error'] ?? '<null>', $responseData['error_code'] ?? 0, $context);
        }

        $this->logger->info('Sending sms message has been finished', [
            'transport' => get_class($this),
            'login' => $this->login,
            'to' => $message->getPhone(),
            'text' => $message->getText(),
        ]);

        return true;
    }

    protected function buildUrl(string $cmd, array $query): string
    {
        $path = "/sys/{$cmd}.php";

        $query = $query + [
            'login' => $this->login,
            'psw' => $this->password,
        ];

        return self::ORIGIN.$path.'?'.http_build_query($query);
    }
}
