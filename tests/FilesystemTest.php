<?php

use Mingalevme\Utils\Str;
use Mingalevme\Utils\Filesystem;
 
class FilesystemTest extends PHPUnit_Framework_TestCase
{
    const FIT_DIR_INTO_SIZE_MIN_FILESIZE = 10240;
    const FIT_DIR_INTO_SIZE_MIN_MAX_FILESIZE = 102400;
    
    const FIT_DIR_INTO_SIZE_MIN_MIN_FILES_COUNT = 10;
    const FIT_DIR_INTO_SIZE_MIN_MAX_FILES_COUNT = 30;
    
    protected static $umask;
    protected static $fitDirIntoSizeDir;
    
    protected static $DR;
    
    public static function setUpBeforeClass()
    {
        $DR = \DIRECTORY_SEPARATOR;
        self::$umask = umask(0);
        self::$fitDirIntoSizeDir = sys_get_temp_dir() . $DR . '_mingalevme-utils' . $DR . 'fit-dir-into-size';
        Filesystem::rmdir(sys_get_temp_dir() . $DR . '_mingalevme-utils');
    }

    /**
     * @dataProvider mkdirAndRmdirDataProvider
     */
    public function testMkdirAndRmdir($dirs)
    {
        $DR = \DIRECTORY_SEPARATOR;
        
        $pathname = sys_get_temp_dir() . $DR . implode($DR, $dirs);
        
        Filesystem::mkdir($pathname, 0775);

        $this->assertTrue(is_dir($pathname), 'Directory has not been created');

        while ($pathname !== sys_get_temp_dir()) {
            $perms = substr(sprintf('%o', fileperms($pathname)), -4);
            $this->assertTrue($perms === '0775', "Permissions for {$pathname} is invalid ($perms)");
            $pathname = dirname($pathname);
        }

        Filesystem::rmdir(sys_get_temp_dir() . $DR . $dirs[0]);

        $this->assertFalse(is_dir(sys_get_temp_dir() . $DR . $dirs[0]), 'Directory has not been deleted: ' . sys_get_temp_dir() . $DR . $dirs[0]);
    }

    public function mkdirAndRmdirDataProvider()
    {
        return [
            [
                [
                    '_mingalevme-utils',
                    '123',
                    '456',
                    '789',
                ]
            ],
        ];
    }
    
    public function testDirsize()
    {
        Filesystem::rmdir(self::$fitDirIntoSizeDir);
        $expected = $this->fillup();
        $actual = Filesystem::dirsize(self::$fitDirIntoSizeDir);
        $this->assertEquals($expected, $actual);
    }
    
    public function testFitDirIntoSize()
    {
        $currentSize = $this->fillup();
        $expected = intval($currentSize / 2);
        Filesystem::fitDirIntoSize(self::$fitDirIntoSizeDir, $expected);
        $actual = Filesystem::dirsize(self::$fitDirIntoSizeDir);
        $this->assertLessThanOrEqual($expected, $actual);
    }
    
    public function testChmod()
    {
        $this->fillup();
        Filesystem::chmod(self::$fitDirIntoSizeDir, 0777);
    }
    
    protected function fillup()
    {
        $DR = \DIRECTORY_SEPARATOR;
        
        $total = 0;
        
        foreach (range(self::FIT_DIR_INTO_SIZE_MIN_MIN_FILES_COUNT, self::FIT_DIR_INTO_SIZE_MIN_MAX_FILES_COUNT) as $i) {
            $dir = implode($DR, [
                self::$fitDirIntoSizeDir,
                Str::random(3),
                Str::random(5),
            ]);
            
            Filesystem::mkdir($dir, 0775);
            
            $filename = $dir . $DR . "tmp-{$i}";
            
            $length = rand(self::FIT_DIR_INTO_SIZE_MIN_FILESIZE, self::FIT_DIR_INTO_SIZE_MIN_MAX_FILESIZE);
            
            file_put_contents($filename, str_repeat('A', $length));
            
            $total += $length;
        }
        
        return $total;
    }

    public static function tearDownAfterClass()
    {
        Filesystem::rmdir(self::$fitDirIntoSizeDir);
        umask(self::$umask);
    }
}
