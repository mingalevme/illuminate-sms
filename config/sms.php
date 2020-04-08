<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default Channel
    |--------------------------------------------------------------------------
    |
    | This option controls the default channel that is used to send any sms
    | messages sent by your application. Alternative channels may be setup
    | and used as needed; however, this channel will be used by default.
    |
    */

    'default' => env('SMS_CHANNEL', 'array'),

    /*
    |--------------------------------------------------------------------------
    | Channel Configurations
    |--------------------------------------------------------------------------
    |
    | Here you may configure all of the channels used by your application plus
    | their respective settings. Several examples have been configured for
    | you and you are free to add your own as your application requires.
    |
    | Package supports a variety of sms "transport" drivers to be used while
    | sending an sms. You will specify which one you are using for your
    | channels below. You are free to add additional channels as required.
    |
    | Supported: "smsc", "log", "null", "array"
    |
    */

    'channels' => [
        'smsc' => [
            'transport' => 'smsc',
            'login' => env('SMS_SMSC_LOGIN'),
            'password' => env('SMS_SMSC_PASSWORD'),
            'shortenLinks' => (bool) env('SMS_SMSC_SHORTEN_LINKS'),
        ],
        'array' => [
            'transport' => 'array',
        ],
        'log' => [
            'transport' => 'log',
            'channel' => env('SMS_LOG_CHANNEL', env('LOG_CHANNEL', 'syslog')),
        ],
        'null' => [
            'transport' => 'null',
        ],
    ],

];
