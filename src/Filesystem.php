<?php

namespace Mingalevme\Utils;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class Filesystem
{
    const FIT_DIR_INTO_SIZE_DIR_SIZE_COUNTING_TIMEOUT = 'fitDirIntoSizeDirSizeCountingTimeout';
    const FIT_DIR_INTO_SIZE_GET_NEXT_FILES_EXECUTION_TIMEOUT = 'fitDirIntoSizeGetNextFilesExecutionTimeout';
    
    /**
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected static $logger;

    public static function setLogger(\Psr\Log\LoggerInterface $logger)
    {
        self::$logger = $logger;
    }
    
    /**
     * Create directory recursively
     * 
     * @param string $pathname
     * @param int $mode
     * @param resource $context
     * @return bool <b>true</b> on success or <b>false</b> on failure.
     */
    public static function mkdir($pathname, $mode = 0777, resource $context = null)
    {
        if (\file_exists($pathname)) {
            return true;
        } elseif ($context) {
            return \mkdir($pathname, $mode, true, $context);
        } else {
            return \mkdir($pathname, $mode, true);
        }
    }
    
    /**
     * Remove directories and their contents recursively
     * 
     * @param string $pathname
     * @return boolean
     */
    public static function rmdir(string $pathname)
    {
        if (\is_dir($pathname) === false) {
            return false;
        }
        
        $files = \array_diff(scandir($pathname), ['.', '..']);
        
        foreach ($files as $file) {
            $subpath = "${pathname}/${file}";
            \is_dir($subpath) ? static::rmdir($subpath) : \unlink($subpath);
        }
        
        return \rmdir($pathname);
    }
    
    /**
     * Recursively changes file mode
     * 
     * @param string $filename Path to the file or directory
     * @param int $mode <p>
     * Note that <i>mode</i> is not automatically
     * assumed to be an octal value, so to ensure the expected operation,
     * you need to prefix <i>mode</i> with a zero (0).
     * Strings such as "g+w" will not work properly.
     * </p>
     * @return boolean
     */
    public static function chmod($filename, $mode)
    {
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($filename));
        
        foreach ($files as $file) {
            /* @var $file \SplFileInfo */
            if ($file->getFilename() !== '..') {
                \chmod(\realpath($file->getPathname()), $mode);
            }
        }
        
        return true;
    }
    
    /**
     * Recursively changes file owner
     * 
     * @param string $filename Path to the file or directory
     * @param int|string $user A user name or number.
     * @return bool
     */
    public static function chown($filename, $user)
    {
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($filename));
        
        foreach ($files as $file) {
            /* @var $file \SplFileInfo */
            if ($file->getFilename() !== '..') {
                \chown(\realpath($file->getPathname()), $user);
            }
        }
        
        return true;
    }
    
    /**
     * Recursively changes file owner
     * 
     * @param string $filename Path to the file or directory
     * @param int|string $group A user name or number.
     * @return bool
     */
    public static function chgrp($filename, $group )
    {
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($filename));
        
        foreach ($files as $file) {
            /* @var $file \SplFileInfo */
            if ($file->getFilename() !== '..') {
                \chgrp(\realpath($file->getPathname()), $group);
            }
        }
        
        return true;
    }

    /**
     * Gets full directory size
     * 
     * @param string $dirname
     * @return int
     * @throws \Exception
     */
    public static function dirsize($dirname)
    {
        if (is_dir($dirname) === false) {
            throw new \Exception("{$dirname} is not a directory");
        }
        
        $size = 0;
        
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dirname, \RecursiveDirectoryIterator::SKIP_DOTS)) as $file) {
            /* @var $file \SplFileInfo */
            $size += $file->getSize();
        }
        
        return $size; 
    }

    /**
     * Truncate directory to specified size in bytes by step-by-step deleting last accessed files.
     * Works only on *nix systrems.
     * 
     * @param string $pathname
     * @param int $size
     * @return boolean
     */
    public static function fitDirIntoSize(string $pathname, int $size, array $options = [])
    {
        $dirSizeCountingTimeout = \array_get($options, self::FIT_DIR_INTO_SIZE_DIR_SIZE_COUNTING_TIMEOUT);
        
        $currentSize = \intval(static::runConsoleCommand("du -sb {$pathname}", null, null, null, $dirSizeCountingTimeout));
        
        if ($currentSize < $size) {
            return true;
        }
        
        $getNextFilesExecutionTimeout = \array_get($options, self::FIT_DIR_INTO_SIZE_GET_NEXT_FILES_EXECUTION_TIMEOUT);
        
        while (count($data = static::getNextFilesForFitDirIntoSize($pathname, null, null, null, $getNextFilesExecutionTimeout)) > 0) {
            foreach ($data as $fileData) {
                list($_, $filesize, $filename) = \explode(' ', $fileData, 3);
                
                $e = null;
                
                try {
                    \unlink($filename);
                } catch (\ErrorException $e) {
                    if (self::$logger) {
                        $errmsg = \sprintf('(%s) Error while deleting file: %s', static::class, $e->getMessage());
                        self::$logger->error($errmsg, [
                            'filename' => $filename,
                        ]);
                    }
                }
                
                if ($e) {
                    $currentSize = \intval(static::runConsoleCommand("du -sb {$pathname}"));
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
    
    /**
     * 
     * @param string         $pathname    The directory path
     * @param string|null    $cwd         The working directory or null to use the working dir of the current PHP process
     * @param array|null     $env         The environment variables or null to use the same environment as the current PHP process
     * @param string|null    $input       The input
     * @param int|float|null $timeout     The timeout in seconds or null to disable
     * @param array          $options     An array of options for proc_open
     * @return string
     */
    protected static function getNextFilesForFitDirIntoSize(string $pathname, $cwd = null, array $env = null, $input = null, $timeout = 60, array $options = array())
    {
        $output = static::runConsoleCommand("find {$pathname} -type f -printf \"%T@ %s %p\n\" | sort -n | head -n1000", $cwd, $env, $input, $timeout, $options);
        $data = \explode(\PHP_EOL, $output);
        unset($data[count($data) - 1]);
        return $data;
    }

    /**
     * Run command line via \Symfony\Component\Process\Process
     *
     * @param string         $commandline The command line to run
     * @param string|null    $cwd         The working directory or null to use the working dir of the current PHP process
     * @param array|null     $env         The environment variables or null to use the same environment as the current PHP process
     * @param string|null    $input       The input
     * @param int|float|null $timeout     The timeout in seconds or null to disable
     * @param array          $options     An array of options for proc_open
     *
     * @throws RuntimeException When proc_open is not installed
     */
    protected static function runConsoleCommand($commandline, $cwd = null, array $env = null, $input = null, $timeout = 60, array $options = array())
    {
        $process = new Process($commandline, $cwd, $env, $input, $timeout, $options);
        
        $process->run();
        
        if ($process->isSuccessful() === false) {
            throw new ProcessFailedException($process);
        }
        
        $output = $process->getOutput();
        
        return $output;
    }
}
