<?php

namespace Mingalevme\Utils;

class Filesystem
{
    public static function mkdir(string $pathname, int $mode = 0777, string $user = null, string $group = null, resource $context = null)
    {
        if (is_dir($pathname)) {
            return true;
        }

        if (is_dir(dirname($pathname)) === false) {
            static::mkdir(dirname($pathname), $mode, $user, $group, $context);
        }

        try {
            if ($context) {
                mkdir($pathname, $mode, false, $context);
            } else {
                mkdir($pathname, $mode);
            }
        } catch (\ErrorException $e) {
            if (strpos(strtolower($e->getMessage()), 'exists') === false) {
                throw $e;
            }
        }

        if ($user) {
            chown($pathname, $user);
        }

        if ($group) {
            chgrp($pathname, $group);
        }

        return true;
    }
}
