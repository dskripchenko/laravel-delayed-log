# laravel-delayed-log

Laravel 的异步日志。日志记录先进入**队列**，再由任务写出，而不是阻塞请求——当日志目标较慢时
（HTTP 日志接收端、外部聚合服务等）这一点很有用。

> 🌐 [English](../../README.md) · [Deutsch](../de/README.md) · [Русский](../ru/README.md) · **中文**

[![Packagist](https://img.shields.io/packagist/v/dskripchenko/laravel-delayed-log)](https://packagist.org/packages/dskripchenko/laravel-delayed-log)
[![License](https://img.shields.io/packagist/l/dskripchenko/laravel-delayed-log)](../../LICENSE.md)

## 环境要求

PHP 8.2–8.5 · Laravel 11 / 12 / 13。

## 安装

```bash
composer require dskripchenko/laravel-delayed-log
```

服务提供者会被自动发现，并向你的 `config/logging.php` 合并一个 `delayed` 通道。

## 用法

把某个通道（或直接把 `LOG_CHANNEL`）指向 `delayed`：

```dotenv
LOG_CHANNEL=delayed

# 日志最终写入的通道（默认 stack）
LOG_DELAYED_CHANNEL=stack
# 写入任务派发到的队列（默认 delayed_log）
LOG_DELAYED_QUEUE=delayed_log
```

```php
use Illuminate\Support\Facades\Log;

Log::channel('delayed')->info('已记录，且没有拖慢请求', ['user' => $id]);
```

每条记录都会作为 `DelayedLogJob` 派发到 `LOG_DELAYED_QUEUE`，再由队列 worker 写入真正的
`LOG_DELAYED_CHANNEL`。因此该队列上必须有 worker 在运行：

```bash
php artisan queue:work --queue=delayed_log
```

## 工作原理

包会注册一个自定义的 `delayed` 通道，其 Monolog 处理器（`DelayedLogHandler`）把每条记录序列化并派发
`DelayedLogJob`。该任务在 worker 进程中把记录重新发到真正的目标通道，于是发起请求的那一侧从不等待日志写入。

## 许可证

[MIT](../../LICENSE.md) © Denis Skripchenko
