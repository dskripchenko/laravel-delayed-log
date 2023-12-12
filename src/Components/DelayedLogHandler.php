<?php

namespace Dskripchenko\LaravelDelayedLog\Components;

use Dskripchenko\LaravelDelayedLog\Interfaces\DelayedLogHandler as DelayedLogHandlerInterface;
use Dskripchenko\LaravelDelayedLog\Jobs\DelayedLogJob;
use Illuminate\Support\Facades\Log;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;

class DelayedLogHandler extends AbstractProcessingHandler implements DelayedLogHandlerInterface
{
    /**
     * @var string
     */
    protected string $queue;

    /**
     * @var string
     */
    protected string $channel;

    /**
     * @var LogRecord
     */
    protected LogRecord $record;

    /**
     * @param string $queue
     * @param string $channel
     * @param int|string|Level $level
     * @param bool $bubble
     */
    public function __construct(string $queue, string $channel, int|string|Level $level = Level::Debug, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->queue = $queue;
        $this->channel = $channel;
    }

    /**
     * @param LogRecord $record
     *
     * @return void
     */
    protected function write(LogRecord $record): void
    {
        $this->record = $record;

        dispatch(new DelayedLogJob($this))
            ->onQueue($this->queue);
    }

    /**
     * @return void
     */
    public function process(): void
    {
        Log::channel($this->channel)
            ->log($this->record->level, $this->record->message, $this->record->context);
    }
}
