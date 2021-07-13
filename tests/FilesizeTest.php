<?php

namespace Mingalevme\Tests\Utils;

use Mingalevme\Utils\Filesize;

class FilesizeTest extends TestCase
{
    /**
     * @dataProvider humanizeDataProvider
     */
    public function testHumanize($humanSize, $byteSize, $precision, $useBinaryPrefix, $exception=null, $emessage=null)
    {
        if ($exception) {
            $this->expectException($exception, $emessage);
        }

        $this->assertEquals($humanSize, Filesize::humanize($byteSize, $precision, $useBinaryPrefix));
    }

    public function humanizeDataProvider()
    {
        return [
            ['512B',    512,                2,  false],
            ['1kB',     1000,               2,  false],
            ['2MB',     2 * pow(1000, 2),   2,  false],
            ['20GB',    20 * pow(1000, 3),  2,  false],

            ['5.65MB',  5.65 * pow(1000, 2),2,  false],
            ['7.89GB',  7.89 * pow(1000, 3),2,  false],

            ['1KiB',    1 * pow(1024, 1),   2,  true],
            ['3GiB',    3 * pow(1024, 3),   2,  true],

            ['1000YB',  1 * pow(1000, 9),   2,  false],
        ];
    }

    /**
     * @dataProvider dehumanizeDataProvider
     */
    public function testDehumanize($byteSize, $humanSize, $exception=null, $emessage=null)
    {
        if ($exception) {
            $this->expectException($exception, $emessage);
        }

        $this->assertEquals($byteSize, Filesize::dehumanize($humanSize));
    }

    public function dehumanizeDataProvider()
    {
        return [
            [1 * pow(1024, 1),  '1024'],
            [1 * pow(1024, 1),  '1024B'],

            [2 * pow(1000, 1),  '2k'],
            [2 * pow(1000, 1),  '2kB'],
            [2 * pow(1024, 1),  '2KiB'],

            [3 * pow(1000, 2),  '3M'],
            [3 * pow(1000, 2),  '3MB'],
            [3 * pow(1024, 2),  '3MiB'],

            [4 * pow(1000, 3),  '4G'],
            [4 * pow(1000, 3),  '4GB'],
            [4 * pow(1024, 3),  '4GiB'],

            [5 * pow(1000, 4),  '5T'],
            [5 * pow(1000, 4),  '5TB'],
            [5 * pow(1024, 4),  '5TiB'],

            [6 * pow(1000, 5),  '6P'],
            [6 * pow(1000, 5),  '6PB'],
            [6 * pow(1024, 5),  '6PiB'],

            [7 * pow(1000, 6),  '7E'],
            [7 * pow(1000, 6),  '7EB'],
            [7 * pow(1024, 6),  '7EiB'],

            [8 * pow(1000, 7),  '8Z'],
            [8 * pow(1000, 7),  '8ZB'],
            [8 * pow(1024, 7),  '8ZiB'],

            [9 * pow(1000, 8),  '9Y'],
            [9 * pow(1000, 8),  '9YB'],
            [9 * pow(1024, 8),  '9YiB'],

            [intval(1.58 * pow(1000, 1)),   '1.58k'],
            [intval(2.20 * pow(1024, 2)),   '2.2MiB'],
            [intval(3.65 * pow(1000, 3)),   '3.65GB'],
            [intval(4.29 * pow(1000, 4)),   '4.29TB'],

            [0, '1bB',  'Mingalevme\Utils\Exception'],
            [0, '2BB',  'Mingalevme\Utils\Exception'],
            [0, '3biB', 'Mingalevme\Utils\Exception'],
            [0, '4BiB', 'Mingalevme\Utils\Exception'],
            [0, '5.2b', 'Mingalevme\Utils\Exception'],
            [0, '6.3B', 'Mingalevme\Utils\Exception'],
            [0, 'err',  'Mingalevme\Utils\Exception'],
            [0, '1024b','Mingalevme\Utils\Exception'],
            [0, '2kiB', 'Mingalevme\Utils\Exception'],

            [0, '7Q',   'Mingalevme\Utils\Exception', 'Invalid size format or unknown/unsupported units'],
        ];
    }

    public static function tearDownAfterClass()
    {
        // pass
    }
}
