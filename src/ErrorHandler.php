<?php

namespace Mingalevme\Utils;

use ErrorException;

class ErrorHandler
{
    protected static $levels = [
        \E_DEPRECATED => 'Deprecated',
        \E_USER_DEPRECATED => 'User Deprecated',
        \E_NOTICE => 'Notice',
        \E_USER_NOTICE => 'User Notice',
        // PHP8.4: Constant E_STRICT (2048) is deprecated
        2048 => 'Runtime Notice',
        \E_WARNING => 'Warning',
        \E_USER_WARNING => 'User Warning',
        \E_COMPILE_WARNING => 'Compile Warning',
        \E_CORE_WARNING => 'Core Warning',
        \E_USER_ERROR => 'User Error',
        \E_RECOVERABLE_ERROR => 'Catchable Fatal Error',
        \E_COMPILE_ERROR => 'Compile Error',
        \E_PARSE => 'Parse Error',
        \E_ERROR => 'Error',
        \E_CORE_ERROR => 'Core Error',
    ];

    /** @var callable|null */
    protected static $handler;

    public static function set(): ?callable
    {
        return static::$handler = set_error_handler(
            /**
             * @throws ErrorException
             */
            function ($type, $message, $file, $line) {
                throw new ErrorException(ErrorHandler::$levels[$type] . ': ' . $message, 0, $type, $file, $line);
            }
        );
    }

    public static function reset(): bool
    {
        return restore_error_handler();
    }

    public static function getErrorHandler(): callable
    {
        $handler = set_error_handler(function () {
        });
        restore_error_handler();
        return $handler;
    }
}
