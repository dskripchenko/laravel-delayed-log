<?php

use Dskripchenko\LaravelDelayedLog\Components\DelayedLogger;
use Monolog\Formatter\LineFormatter;

return [
    'channels' => [
        'delayed' => [
            'driver' => 'custom',
            'channel' => env('LOG_DELAYED_CHANNEL', 'stack'),
            'queue' =>  env('LOG_DELAYED_QUEUE', 'delayed_log'),
            'via' => DelayedLogger::class,
            'formatter' => [
                'class' => LineFormatter::class,
                'options' => [
                    LineFormatter::class => [
                        // keep formatter construct options
                    ]
                ]
            ]
        ]
    ],

];
