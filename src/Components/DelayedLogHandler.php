<?php

namespace Dskripchenko\LaravelDelayedLog\Components;

use DateTimeImmutable;
use Dskripchenko\LaravelDelayedLog\Interfaces\DelayedLogHandler as DelayedLogHandlerInterface;
use Dskripchenko\LaravelDelayedLog\Jobs\DelayedLogJob;
use Exception;
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
     * @return string
     */
    public function queue(): string
    {
        return $this->queue;
    }

    /**
     * @return array
     */
    public function __serialize(): array
    {
        return [
            'queue' => $this->queue,
            'channel' => $this->channel,
            'level' => $this->level,
            'bubble' => $this->bubble,
            'record' => [
                'message' => $this->record->message,
                'level' => $this->record->level->getName(),
                'channel' => $this->record->channel,
                'datetime' => $this->record->datetime,
                'context' => data_get($this->record->formatted, 'context'),
                'extra' => data_get($this->record->formatted, 'extra'),
            ]
        ];
    }

    /**
     * @param array $data
     *
     * @return void
     */
    public function __unserialize(array $data): void
    {
        $this->queue = data_get($data, 'queue');
        $this->channel = data_get($data, 'channel');
        $this->level = data_get($data, 'level');
        $this->bubble = data_get($data, 'bubble');

        $message = data_get($data, 'record.message');
        $level = data_get($data, 'record.level');
        $channel = data_get($data, 'record.channel');
        $context = data_get($data, 'record.context');
        $extra = data_get($data, 'record.extra');

        try {
            $datetime = new DateTimeImmutable(data_get($data, 'record.datetime'));
        }
        catch (Exception) {
            $datetime = new DateTimeImmutable();
        }

        $this->record = new LogRecord(
            $datetime,
            $channel,
            Level::fromName($level),
            $message,
            $context,
            $extra
        );
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
            ->log($this->record->level->getName(), $this->record->message, $this->record->context);
    }
}
