# laravel-delayed-log

Асинхронное логирование для Laravel. Записи уходят в **очередь** и пишутся
джобой, а не в ходе запроса, — это помогает, когда приёмник логов медленный:
HTTP-приёмники, внешние агрегаторы и тому подобное.

> 🌐 [English](../../README.md) · [Deutsch](../de/README.md) · **Русский** · [中文](../zh/README.md)

[![Packagist](https://img.shields.io/packagist/v/dskripchenko/laravel-delayed-log)](https://packagist.org/packages/dskripchenko/laravel-delayed-log)
[![License](https://img.shields.io/packagist/l/dskripchenko/laravel-delayed-log)](../../LICENSE.md)

## Требования

PHP 8.2–8.5 · Laravel 11 / 12 / 13.

## Установка

```bash
composer require dskripchenko/laravel-delayed-log
```

Провайдер подхватывается автоматически и добавляет канал `delayed` в ваш
`config/logging.php`.

## Использование

Направьте канал (или сам `LOG_CHANNEL`) на `delayed`:

```dotenv
LOG_CHANNEL=delayed

# Канал, в который запись попадёт в итоге (по умолчанию stack)
LOG_DELAYED_CHANNEL=stack
# Очередь, в которую уходит джоба записи (по умолчанию delayed_log)
LOG_DELAYED_QUEUE=delayed_log
```

```php
use Illuminate\Support\Facades\Log;

Log::channel('delayed')->info('Записано, не задержав запрос', ['user' => $id]);
```

Каждая запись отправляется джобой `DelayedLogJob` в очередь `LOG_DELAYED_QUEUE`,
а воркер пишет её в настоящий канал `LOG_DELAYED_CHANNEL`. Значит, воркер на этой
очереди должен быть запущен:

```bash
php artisan queue:work --queue=delayed_log
```

## Как устроено

Пакет регистрирует свой канал `delayed`; его Monolog-обработчик
(`DelayedLogHandler`) сериализует запись и отправляет `DelayedLogJob`. Джоба уже
из воркера повторяет запись в целевой канал — исходный запрос при этом не ждёт
записи лога.

## Лицензия

[MIT](../../LICENSE.md) © Денис Скрипченко
