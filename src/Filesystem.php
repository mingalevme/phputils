<?php

namespace Mingalevme\Utils;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Filesystem
{
    /**
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected static $logger;

    const UNIT_PREFIXES_POWERS = [
        ''  => 0,
        'b' => 0,
        'B' => 0,
        'K' => 1,
        'M' => 2,
        'G' => 3,
        'T' => 4,
        'P' => 5,
        'E' => 6,
        'Z' => 7,
        'Y' => 8,
    ];
    
    public static function setLogger(\Psr\Log\LoggerInterface $logger)
    {
        self::$logger = $logger;
    }

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
     * @param mixed $size
     * @todo add support for human readable sizes: K, M, G, T, P, E, Z, Y (power 1024) or KB, MB, … (power 1000).
     * @return boolean
     */
    public static function fitDirIntoSize(string $pathname, $size)
    {
        $currentSize = intval(static::runConsoleCommand("du -sb {$pathname}"));
        
        if ($currentSize < $size) {
            return true;
        }
        
        while (count($data = static::getNextFilesForFitDirIntoSize($pathname)) > 0) {
            foreach ($data as $fileData) {
                list($_, $filesize, $filename) = explode(' ', $fileData, 3);
                
                $e = null;
                
                try {
                    unlink($filename);
                } catch (\ErrorException $e) {
                    if (self::$logger) {
                        $errmsg = sprintf('(%s) Error while deleting file: %s', static::class, $e->getMessage());
                        self::$logger->error($errmsg, [
                            'filename' => $filename,
                        ]);
                    }
                }
                
                if ($e) {
                    $currentSize = intval(static::runConsoleCommand("du -sb {$pathname}"));
                } else {
                    $currentSize = $currentSize - (int) $filesize;
                }
                
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
    
    public static function humanizeSize(int $size, int $precision = 2, $useMetricPrefix = true)
    {
        $base = $useMetricPrefix ? 1000 : 1024;
        
        foreach (self::UNIT_PREFIXES_POWERS as $prefix => $exp) {
            if ($size < pow($base, $exp + 1)) {
                return round($size / pow($base, $exp), $precision) . $prefix . ($useMetricPrefix ? 'b' : 'iB');
            }
        }
        
        throw new Exception('Size is too big');
    }
    
    /**
     * GB, G - 1000, GiB - 1024
     * 
     * @param string $size E.g. 300M, 1.5GiB
     * @throws Exception
     */
    public static function unhumanizeSize(string $size)
    {
        if (preg_match('/\d+\.\d+b/', $size)) {
            throw new Exception("Invalid size format or unknown/unsupported units");
        }
        
        $supportedUnits = implode('', array_keys(self::UNIT_PREFIXES_POWERS));
        $regexp = "/^(\d+(?:\.\d+)?)(([{$supportedUnits}])((?<!b|B)(b|B|iB))?)?$/";
        
        if ((bool) preg_match($regexp, $size, $matches) === false) {
            throw new Exception("Invalid size format or unknown/unsupported units");
        }
        
        $prefix = isset($matches[3]) ? $matches[3] : 'b';
        
        $base = isset($matches[4]) && $matches[4] === 'iB' ? 1024 : 1000;
        
        if (strpos($matches[1], '.') !== false) {
            return intval(floatval($matches[1]) * pow($base, self::UNIT_PREFIXES_POWERS[$prefix]));
        } else {
            return intval($matches[1]) * pow($base, self::UNIT_PREFIXES_POWERS[$prefix]);
        }
    }
}
