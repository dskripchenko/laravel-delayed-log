# laravel-delayed-log

Asynchrones Logging für Laravel. Log-Einträge wandern in eine **Queue** und
werden von einem Job geschrieben, statt den Request zu blockieren — nützlich,
wenn das Ziel langsam ist: HTTP-Log-Senken, externe Aggregatoren und Ähnliches.

> 🌐 [English](../../README.md) · **Deutsch** · [Русский](../ru/README.md) · [中文](../zh/README.md)

[![Packagist](https://img.shields.io/packagist/v/dskripchenko/laravel-delayed-log)](https://packagist.org/packages/dskripchenko/laravel-delayed-log)
[![License](https://img.shields.io/packagist/l/dskripchenko/laravel-delayed-log)](../../LICENSE.md)

## Voraussetzungen

PHP 8.2–8.5 · Laravel 11 / 12 / 13.

## Installation

```bash
composer require dskripchenko/laravel-delayed-log
```

Der Service Provider wird automatisch erkannt und ergänzt Ihre
`config/logging.php` um einen Kanal `delayed`.

## Verwendung

Richten Sie einen Kanal (oder gleich `LOG_CHANNEL`) auf `delayed`:

```dotenv
LOG_CHANNEL=delayed

# Kanal, in den der Eintrag am Ende geschrieben wird (Standard: stack)
LOG_DELAYED_CHANNEL=stack
# Queue, auf der der Schreib-Job landet (Standard: delayed_log)
LOG_DELAYED_QUEUE=delayed_log
```

```php
use Illuminate\Support\Facades\Log;

Log::channel('delayed')->info('Geschrieben, ohne den Request aufzuhalten', ['user' => $id]);
```

Jeder Eintrag wird als `DelayedLogJob` auf `LOG_DELAYED_QUEUE` gelegt; ein
Queue-Worker schreibt ihn anschließend in den eigentlichen Kanal
`LOG_DELAYED_CHANNEL`. Für diese Queue muss also ein Worker laufen:

```bash
php artisan queue:work --queue=delayed_log
```

## Funktionsweise

Das Paket registriert einen eigenen Kanal `delayed`, dessen Monolog-Handler
(`DelayedLogHandler`) jeden Eintrag serialisiert und `DelayedLogJob` versendet.
Der Job gibt den Eintrag aus einem Worker-Prozess auf dem echten Zielkanal
erneut aus — der ursprüngliche Request wartet damit nie auf den Log-Schreibvorgang.

## Lizenz

[MIT](../../LICENSE.md) © Denis Skripchenko
