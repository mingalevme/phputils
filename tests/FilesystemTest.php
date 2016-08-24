<?php
 
use Mingalevme\Utils\Filesystem;
 
class FilesystemTest extends PHPUnit_Framework_TestCase
{
    protected static $umask;

    protected static $data = [
        [
            '_mingalevme-utils',
            '123',
            '456',
            '789',
        ],
    ];
    
    public static function setUpBeforeClass()
    {
        self::$umask = umask(0);
    }

    public function test()
    {
        $DR = \DIRECTORY_SEPARATOR;
        
        foreach (self::$data as $dirs) {
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
    }
    
    public static function tearDownAfterClass()
    {
        umask(self::$umask);
    }
}
