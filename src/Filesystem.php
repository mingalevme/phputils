<?php

namespace Mingalevme\Utils;

class Filesystem
{
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
     * @throws \ErrorException
     * @return bool <b>true</b> on success or <b>false</b> on failure.
     */
    public static function mkdir($pathname, $mode = 0777, $context = null)
    {
        if (\file_exists($pathname)) {
            return true;
        }

        $result = null;

        try {
            $result = $context
                ? @\mkdir($pathname, $mode, true, $context)
                : @\mkdir($pathname, $mode, true);
        } catch (\ErrorException $e) {
            \clearstatcache(true, $pathname);
        }

        if ($result) {
            return $result;
        }

        if ($result === false) {
            $error = error_get_last();
            $e = new \ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);
        }

        if (\strpos(\strtolower($e->getMessage()), 'file exists') !== false) {
            return true;
        }

        if (\file_exists($pathname)) {
            return true;
        }
        
        throw $e;
    }
    
    /**
     * Remove directories and their contents recursively
     * 
     * @param string $pathname
     * @return boolean
     */
    public static function rmdir($pathname)
    {
        if (\is_dir($pathname) === false) {
            return false;
        }
        
        $files = \array_diff(scandir($pathname), ['.', '..']);
        
        foreach ($files as $file) {
            $subpath = "${pathname}/${file}";
            \is_dir($subpath) ? static::rmdir($subpath) : static::unlink($subpath);
        }
        
        return \rmdir($pathname);
    }

    /**
     * Safely remove file
     *
     * @param string $pathname Path to the file
     * @return bool Returns TRUE on success or FALSE on failure.
     * @throws \ErrorException
     */
    public static function unlink($pathname, $context = null)
    {
        $result = null;

        try {
            $result = $context
                ? @\unlink($pathname, $context)
                : @\unlink($pathname);
        } catch (\ErrorException $e) {
            // pass
        }

        if ($result) {
            return true;
        }

        if (!\file_exists($pathname)) {
            return true;
        }

        if ($result === false) {
            $error = error_get_last();
            $e = new \ErrorException($error['message'], 0, $error['type'], $error['file'], $error['line']);
        }

        throw $e;
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
}
