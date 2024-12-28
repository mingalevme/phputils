<?php

declare(strict_types=1);

namespace Mingalevme\Utils;

use Throwable;

/**
 * @readonly
 */
class Retryer
{
    /**
     * @param int<1, max> $tries
     * @param list<Throwable>|null $allowable
     * @return mixed
     * @throws Throwable
     */
    public static function run(callable $task, int $tries = 3, ?array $allowable = null)
    {
        for ($i = 0; $i < $tries; $i++) {
            try {
                return $task();
            } catch (Throwable $e) {
                if ($allowable && !in_array(get_class($e), $allowable)) {
                    break;
                }
            }
        }

        /** @var Throwable $e */
        throw $e;
    }
}