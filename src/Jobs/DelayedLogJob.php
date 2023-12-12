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
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
