<?php

namespace Dskripchenko\LaravelDelayedLog\Components;

use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

class DelayedLogger
{
    /**
     * @param  array  $config
     * @return LoggerInterface
     */
    public function __invoke(array $config): LoggerInterface
    {
        $handler = $this->getHandler($config);
        $handler->setFormatter($this->getFormatter($config));
        return new Logger($this->getLoggerName(), [$handler]);
    }

    /**
     * @return string
     */
    public function getLoggerName(): string
    {
        return 'logstash';
    }

    /**
     * @param array $config
     *
     * @return DelayedLogHandler
     */
    protected function getHandler(array $config): DelayedLogHandler
    {
        $queue = data_get($config, 'queue', 'delayed_log');
        $channel = data_get($config, 'channel', 'stack');
        return new DelayedLogHandler($queue, $channel);
    }

    /**
     * @param array $config
     *
     * @return FormatterInterface
     */
    protected function getFormatter(array $config): FormatterInterface
    {
        $formatter = data_get($config, 'formatter.class', LineFormatter::class);
        $options = (array) data_get($config, "formatter.options.{$formatter}");
        $formatter = app($formatter, $options);

        if (!($formatter instanceof FormatterInterface)) {
            return new LineFormatter();
        }

        return $formatter;
    }

}
