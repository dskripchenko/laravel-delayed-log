# laravel-delayed-log

Asynchronous logging for Laravel. Log records are pushed onto a **queue** and
written by a job instead of blocking the request — useful when your log target
is slow (HTTP log sinks, external aggregators, etc.).

[![Packagist](https://img.shields.io/packagist/v/dskripchenko/laravel-delayed-log)](https://packagist.org/packages/dskripchenko/laravel-delayed-log)
[![License](https://img.shields.io/packagist/l/dskripchenko/laravel-delayed-log)](LICENSE)

## Requirements

PHP 8.2–8.5 · Laravel 11 / 12 / 13.

## Install

```bash
composer require dskripchenko/laravel-delayed-log
```

The service provider is auto-discovered and merges a `delayed` log channel into
your `config/logging.php`.

## Usage

Point a channel (or your default `LOG_CHANNEL`) at `delayed`:

```dotenv
LOG_CHANNEL=delayed

# The channel the records are ultimately written to (default: stack)
LOG_DELAYED_CHANNEL=stack
# The queue the write job is dispatched on (default: delayed_log)
LOG_DELAYED_QUEUE=delayed_log
```

```php
use Illuminate\Support\Facades\Log;

Log::channel('delayed')->info('Handled without blocking the request', ['user' => $id]);
```

Each record is dispatched as a `DelayedLogJob` onto `LOG_DELAYED_QUEUE`; a queue
worker then writes it to the underlying `LOG_DELAYED_CHANNEL`. Make sure a
worker is processing that queue:

```bash
php artisan queue:work --queue=delayed_log
```

## How it works

The package registers a custom `delayed` channel whose Monolog handler
(`DelayedLogHandler`) serializes each record and dispatches `DelayedLogJob`. The
job re-emits the record on the real target channel from a worker process, so the
originating request never waits on the log write.

## License

[MIT](LICENSE) © Denis Skripchenko
