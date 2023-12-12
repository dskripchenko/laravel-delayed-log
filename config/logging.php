<?php

use Dskripchenko\LaravelDelayedLog\Components\DelayedLogger;
use Monolog\Formatter\NormalizerFormatter;

return [
    'channels' => [
        'delayed' => [
            'driver' => 'custom',
            'channel' => env('LOG_DELAYED_CHANNEL', 'stack'),
            'queue' =>  env('LOG_DELAYED_QUEUE', 'delayed_log'),
            'via' => DelayedLogger::class,
            'formatter' => [
                'dateformat' => NormalizerFormatter::SIMPLE_DATE
            ]
        ]
    ],

];
