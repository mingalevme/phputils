<?php

namespace Mingalevme\Utils;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

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
    
    public static function rmdir(string $pathname)
    {
        $files = array_diff(scandir($pathname), ['.', '..']);
        
        foreach ($files as $file) {
            $subpath = "${pathname}/${file}";
            is_dir($subpath) ? static::rmdir($subpath) : unlink($subpath);
        }
        
        return rmdir($pathname);
    }
    
    /**
     * Truncate directory to specified size in bytes by step-by-step deleting last accessed files
     * 
     * @param string $pathname
     * @param int $size
     * @todo add support for human readable sizes: K, M, G, T, P, E, Z, Y (power 1024) or KB, MB, … (power 1000).
     * @return boolean
     */
    public static function fitDirIntoSize(string $pathname, int $size)
    {
        $currentSize = intval(static::runConsoleCommand("du -sb {$pathname}"));
        
        if ($currentSize < $size) {
            return true;
        }
        
        while (count($data = static::getNextFilesForFitDirIntoSize($pathname)) > 0) {
            foreach ($data as $fileData) {
                list($_, $filesize, $filename) = explode(' ', $fileData, 3);
                unlink($filename);
                $currentSize = $currentSize - (int) $filesize;
                if ($currentSize < $size) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    protected static function getNextFilesForFitDirIntoSize(string $pathname)
    {
        $data = explode(\PHP_EOL, static::runConsoleCommand("find {$pathname} -type f -printf \"%T@ %s %p\n\" | sort -n | head -n1000"));
        unset($data[count($data) - 1]);
        return $data;
    }

    protected static function runConsoleCommand($command)
    {
        $process = new Process($command);
        
        $process->run();
        
        if ($process->isSuccessful() === false) {
            throw new ProcessFailedException($process);
        }
        
        $output = $process->getOutput();
        
        return $output;
    }
}
