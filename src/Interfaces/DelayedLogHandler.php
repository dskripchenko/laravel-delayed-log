<?php

namespace Dskripchenko\LaravelDelayedLog\Interfaces;


interface DelayedLogHandler
{
    public function process(): void;

    public function queue(): string;
}
