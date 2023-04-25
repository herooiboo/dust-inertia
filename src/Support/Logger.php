<?php

namespace Dust\Support;

use Throwable;
use Illuminate\Support\Facades\Log;

class Logger
{
    public static function info(string $message, array $context = [], string $channel = null): void
    {
        static::log('info', $message, $context, $channel);
    }

    public static function warning(string $message, array $context = [], string $channel = null): void
    {
        static::log('warning', $message, $context, $channel);
    }

    public static function notice(string $message, array $context = [], string $channel = null): void
    {
        static::log('notice', $message, $context, $channel);
    }

    public static function debug(string $message, array $context = [], string $channel = null): void
    {
        static::log('debug', $message, $context, $channel);
    }

    public static function error(string $message, Throwable $e, array $context = [], string $channel = null): void
    {
        static::log('error', $message,
            array_merge($context, static::buildExceptionContext($e)), $channel);
    }

    public static function emergency(string $message, Throwable $e, array $context = [], string $channel = null): void
    {
        static::log('emergency', $message,
            array_merge($context, static::buildExceptionContext($e)), $channel);
    }

    public static function log(string $level, string $message, array $context, string|null $channel): void
    {
        Log::channel(self::resolveChannel($channel, $level))->log($level, $message, $context);
    }

    protected static function buildExceptionContext(Throwable $e): array
    {
        return [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ];
    }

    protected static function resolveChannel(string|null $channel, $level): string
    {
        $channel = $channel ?: config("nebula.logging.channels.$level");

        return $channel && self::validChannel($channel) ? $channel : config('logging.default');
    }

    protected static function validChannel(mixed $channel): bool
    {
        return array_key_exists($channel, config('logging.channels', []));
    }
}
