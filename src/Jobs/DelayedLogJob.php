<?php

namespace Dskripchenko\LaravelDelayedLog\Jobs;

use Dskripchenko\LaravelDelayedLog\Interfaces\DelayedLogHandler;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class DelayedLogJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 1;

    /**
     * @var DelayedLogHandler
     */
    public DelayedLogHandler $handler;

    /**
     * @param DelayedLogHandler $handler
     */
    public function __construct(DelayedLogHandler $handler)
    {
        $this->handler = $handler;
        $this->onQueue($handler->queue());
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->handler->process();
    }
}
