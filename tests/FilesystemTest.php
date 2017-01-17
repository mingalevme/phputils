<?php
 
use Mingalevme\Utils\Filesystem;
 
class FilesystemTest extends PHPUnit_Framework_TestCase
{
    protected static $umask;
    
    public static function setUpBeforeClass()
    {
        self::$umask = umask(0);
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
    
    /**
     * @dataProvider humanizeSizeDataProvider
     */
    public function testHumanizeSize($humanSize, $byteSize, $precision, $useMetricPrefix, $exception=null, $emessage=null)
    {
        if ($exception) {
            $this->setExpectedException($exception, $emessage);
        }
        
        $this->assertEquals($humanSize, Filesystem::humanizeSize($byteSize, $precision, $useMetricPrefix));
    }
    
    public function humanizeSizeDataProvider()
    {
        return [
            ['512b',    512,                2,  true],
            ['1Kb',     1000,               2,  true],
            ['2Mb',     2*pow(1000, 2),     2,  true],
            
            ['5.65Mb',  5.65*pow(1000, 2),  2,  true],
            ['7.89Gb',  7.89*pow(1000, 3),  2,  true],
            
            ['1KiB',    1*pow(1024, 1),     2,  false],
            ['3GiB',    3*pow(1024, 3),     2,  false],
        ];
    }
    
    /**
     * @dataProvider unhumanizeSizeDataProvider
     */
    public function testUnhumanizeSize($humanSize, $byteSize, $exception=null, $emessage=null)
    {
        if ($exception) {
            $this->setExpectedException($exception, $emessage);
        }
        
        $this->assertEquals($byteSize, Filesystem::unhumanizeSize($humanSize));
    }
    
    public function unhumanizeSizeDataProvider()
    {
        return [
            ['1024' , 1024],
            ['1024b' , 1024],
            ['1024B' , 1024],
            
            ['2K'   , 2 * 1000],
            ['2Kb'  , 2 * 1000],
            ['2KB'  , 2 * 1000],
            ['2KiB' , 2 * 1024],
            
            ['3M'   , 3 * pow(1000, 2)],
            ['3Mb'  , 3 * pow(1000, 2)],
            ['3MB'  , 3 * pow(1000, 2)],
            ['3MiB' , 3 * pow(1024, 2)],
            
            ['4G'   , 4 * pow(1000, 3)],
            ['4Gb'  , 4 * pow(1000, 3)],
            ['4GB'  , 4 * pow(1000, 3)],
            ['4GiB' , 4 * pow(1024, 3)],
            
            ['5T'   , 5 * pow(1000, 4)],
            ['5Tb'  , 5 * pow(1000, 4)],
            ['5TiB' , 5 * pow(1024, 4)],
            
            ['6P'   , 6 * pow(1000, 5)],
            ['6Pb'  , 6 * pow(1000, 5)],
            ['6PiB' , 6 * pow(1024, 5)],
            
            ['7E'   , 7 * pow(1000, 6)],
            ['7Eb'  , 7 * pow(1000, 6)],
            ['7EiB' , 7 * pow(1024, 6)],
            
            ['8Z'   , 8 * pow(1000, 7)],
            ['8Zb'  , 8 * pow(1000, 7)],
            ['8ZiB' , 8 * pow(1024, 7)],
            
            ['9Y'   , 9 * pow(1000, 8)],
            ['9Yb'  , 9 * pow(1000, 8)],
            ['9YiB' , 9 * pow(1024, 8)],
            
            ['2.2K' , intval(2.2 * pow(1000, 1))],
            ['3.3MiB' , intval(3.3 * pow(1024, 2))],
            
            ['1bB'  , 0, 'Mingalevme\Utils\Exception'],
            ['2BB'  , 0, 'Mingalevme\Utils\Exception'],
            ['3biB' , 0, 'Mingalevme\Utils\Exception'],
            ['4BiB' , 0, 'Mingalevme\Utils\Exception'],
            ['5.2b' , 0, 'Mingalevme\Utils\Exception'],
            ['err'  , 0, 'Mingalevme\Utils\Exception'],
            
            ['7Q'   , 0, 'Mingalevme\Utils\Exception', 'Invalid size format or unknown/unsupported units'],
        ];
    }

    public static function tearDownAfterClass()
    {
        umask(self::$umask);
    }
}
