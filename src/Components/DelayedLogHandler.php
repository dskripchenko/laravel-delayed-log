<?php

namespace Dskripchenko\LaravelDelayedLog\Components;

use Dskripchenko\LaravelDelayedLog\Interfaces\DelayedLogHandler as DelayedLogHandlerInterface;
use Dskripchenko\LaravelDelayedLog\Jobs\DelayedLogJob;
use Illuminate\Support\Facades\Log;
use Laravel\SerializableClosure\Exceptions\PhpVersionNotSupportedException;
use Laravel\SerializableClosure\SerializableClosure;
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
     * @throws PhpVersionNotSupportedException
     */
    public function __serialize(): array
    {
        $record = $this->record;
        $serializedRecord = serialize(new SerializableClosure(function () use ($record) {
            return $record;
        }));

        return [
            'queue' => $this->queue,
            'channel' => $this->channel,
            'level' => $this->level,
            'bubble' => $this->bubble,
            'record' => $serializedRecord
        ];
    }

    /**
     * @param array $data
     *
     * @return void
     * @throws PhpVersionNotSupportedException
     */
    public function __unserialize(array $data): void
    {
        $this->queue = data_get($data, 'queue');
        $this->channel = data_get($data, 'channel');
        $this->level = data_get($data, 'level');
        $this->bubble = data_get($data, 'bubble');

        $record = (string) data_get($data, 'record');
        /** @var SerializableClosure $serializableClosure */
        $serializableClosure = unserialize($record, ['allowed_classes' => [SerializableClosure::class]]);
        $closure = $serializableClosure->getClosure();
        $this->record = $closure();
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
