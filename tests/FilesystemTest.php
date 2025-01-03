<?php

declare(strict_types=1);

namespace Mingalevme\Tests\Utils;

use Mingalevme\Utils\Str;
use Mingalevme\Utils\Filesystem;
 
final class FilesystemTest extends TestCase
{
    public const FIT_DIR_INTO_SIZE_MIN_FILESIZE = 10240;
    public const FIT_DIR_INTO_SIZE_MIN_MAX_FILESIZE = 102400;

    public const FIT_DIR_INTO_SIZE_MIN_MIN_FILES_COUNT = 10;
    public const FIT_DIR_INTO_SIZE_MIN_MAX_FILES_COUNT = 30;

    protected static $umask;
    protected static $fitDirIntoSizeDir;

    protected static $DR;

    public static function setUpBeforeClass(): void
    {
        $DR = \DIRECTORY_SEPARATOR;
        self::$umask = umask(0);
        self::$fitDirIntoSizeDir = sys_get_temp_dir() . $DR . '_mingalevme-utils' . $DR . 'fit-dir-into-size';
        Filesystem::rmdir(sys_get_temp_dir() . $DR . '_mingalevme-utils');
    }

    /**
     * @dataProvider mkdirAndRmdirDataProvider
     */
    public function testMkdirAndRmdir($dirs): void
    {
        $DR = \DIRECTORY_SEPARATOR;

        $pathname = sys_get_temp_dir() . $DR . implode($DR, $dirs);

        Filesystem::mkdir($pathname, 0775);

        $this->assertTrue(is_dir($pathname), 'Directory has not been created');

        Filesystem::mkdir($pathname, 0775); // Cheking creating of existing directory

        Filesystem::rmdir(sys_get_temp_dir() . $DR . $dirs[0]);

        $this->assertFalse(is_dir(sys_get_temp_dir() . $DR . $dirs[0]), 'Directory has not been deleted: ' . sys_get_temp_dir() . $DR . $dirs[0]);
    }

    public static function mkdirAndRmdirDataProvider(): array
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

    public function testMkdirOnExistingDirWithoutErrorException(): void
    {
        $dirname = implode(\DIRECTORY_SEPARATOR, [sys_get_temp_dir(), '_mingalevme-utils', 'existing-dir']);

        Filesystem::mkdir($dirname);

        $this->assertDirectoryExists($dirname);

        $this->assertTrue(Filesystem::mkdir($dirname));
    }

    public function testMkdirOnExistingDirWithErrorException(): void
    {
        set_error_handler(function($type, $message, $file, $line){
            var_dump(func_get_args());
        });

        $dirname = implode(\DIRECTORY_SEPARATOR, [sys_get_temp_dir(), '_mingalevme-utils', 'existing-dir']);

        Filesystem::mkdir($dirname);

        $this->assertDirectoryExists($dirname);

        $this->assertTrue(Filesystem::mkdir($dirname));
    }

    public function testUnlinkWithoutErrorException(): void
    {
        $filename = sys_get_temp_dir() . '/_mingalevme-test';

        touch($filename);

        $this->assertFileExists($filename);

        Filesystem::unlink($filename);

        $this->appAssertFileDoesNotExist($filename);

        $this->assertTrue(Filesystem::unlink($filename));
    }

    public function testUnlinkWithErrorException(): void
    {
        set_error_handler(function($type, $message, $file, $line){
            var_dump(func_get_args());
        });

        $filename = sys_get_temp_dir() . '/_mingalevme-test';

        touch($filename);

        $this->assertFileExists($filename);

        Filesystem::unlink($filename);

        $this->appAssertFileDoesNotExist($filename);

        $this->assertTrue(Filesystem::unlink($filename));
    }

    public function testDirsize(): void
    {
        Filesystem::rmdir(self::$fitDirIntoSizeDir);
        $expected = $this->fillUp();
        $actual = Filesystem::dirsize(self::$fitDirIntoSizeDir);
        $this->assertEquals($expected, $actual);
    }

    /*public function testFitDirIntoSize()
    {
        $currentSize = $this->fillup();
        $expected = intval($currentSize / 2);
        Filesystem::fitDirIntoSize(self::$fitDirIntoSizeDir, $expected);
        clearstatcache();
        $actual = Filesystem::dirsize(self::$fitDirIntoSizeDir);
        $this->assertLessThanOrEqual($expected, $actual);
    }*/

    public function testChmod(): void
    {
        $this->fillUp();
        Filesystem::chmod(self::$fitDirIntoSizeDir, 0777);
        $this->addToAssertionCount(1);
    }

    private function fillUp(): int
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

            //$total += $length;
            $total += filesize($filename);
        }

        return $total;
    }

    public static function tearDownAfterClass(): void
    {
        Filesystem::rmdir(self::$fitDirIntoSizeDir);
        umask(self::$umask);
    }

    private function appAssertFileDoesNotExist(string $filename): void
    {
        self::assertFalse(file_exists($filename), "file \"$filename\" exists");
    }
}
