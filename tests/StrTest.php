<?php

use Mingalevme\Utils\Str as S;

class StrTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider snakeizeDataProvider
     * @param string $expected
     * @param string $str
     * @param string $mode LOWER|UPPER
     */
    public function testSnakeize($expected, $str, $mode)
    {
        $this->assertSame($expected, S::snakeize($str, $mode));
    }
    
    public function snakeizeDataProvider()
    {
        return [
            [
                'simple_test',
                'simple_test',
                S::LOWER,
            ],
            [
                'Simple_test',
                'simple_test',
                S::UPPER,
            ],
            
            [
                'simple_test',
                'simpleTest',
                S::LOWER,
            ],
            [
                'Simple_test',
                'simpleTest',
                S::UPPER,
            ],
            
            [
                'easy',
                'easy',
                S::LOWER,
            ],
            [
                'Easy',
                'easy',
                S::UPPER,
            ],
            
            [
                'html',
                'HTML',
                S::LOWER,
            ],
            [
                'Html',
                'HTML',
                S::UPPER,
            ],
            
            [
                'simple_xml',
                'simpleXML',
                S::LOWER,
            ],
            [
                'Simple_xml',
                'simpleXML',
                S::UPPER,
            ],
            
            [
                'pdf_load',
                'PDFLoad',
                S::LOWER,
            ],
            [
                'Pdf_load',
                'PDFLoad',
                S::UPPER,
            ],
            
            [
                'start_middle_last',
                'startMIDDLELast',
                S::LOWER,
            ],
            [
                'Start_middle_last',
                'startMIDDLELast',
                S::UPPER,
            ],
            
            [
                'a_string',
                'AString',
                S::LOWER,
            ],
            [
                'A_string',
                'AString',
                S::UPPER,
            ],
            
            [
                'some4_numbers234',
                'Some4Numbers234',
                S::LOWER,
            ],
            [
                'Some4_numbers234',
                'Some4Numbers234',
                S::UPPER,
            ],
            
            [
                'test123_string',
                'TEST123String',
                S::LOWER,
            ],
            [
                'Test123_string',
                'TEST123String',
                S::UPPER,
            ],
            
            [
                'test_test_with_spaces',
                'TestTest With   spaces',
                S::LOWER,
            ],
            [
                'Test_test_with_spaces',
                'TestTest With   spaces',
                S::UPPER,
            ],
        ];
    }
    
    /**
     * @dataProvider camelizeDataProvider
     * @param string $str
     */
    public function _testCamelize($expected, $str, $mode)
    {
        $this->assertSame($expected, S::camelize($str, $mode));
    }
    
    public function camelizeDataProvider()
    {
        return [
            [
                'simpleTest',
                'simple_test',
                S::LOWER,
            ],
            [
                'simpleTest',
                'simple-test',
                S::LOWER,
            ],
            [
                'SimpleTest',
                'simple-test',
                S::UPPER,
            ],
            [
                'SimpleTest',
                'simple-test',
                S::UPPER,
            ],
            
            [
                'easy',
                'easy',
                S::LOWER,
            ],
            [
                'Easy',
                'easy',
                S::UPPER,
            ],
            
            [
                'html',
                'HTML',
                S::LOWER,
            ],
            [
                'Html',
                'HTML',
                S::UPPER,
            ],
            
            [
                'simpleXml',
                'simple_xml',
                S::LOWER,
            ],
            [
                'simpleXml',
                'simple-xml',
                S::LOWER,
            ],
            [
                'SimpleXml',
                'simple_xml',
                S::UPPER,
            ],
            [
                'SimpleXml',
                'simple-xml',
                S::UPPER,
            ],
            
            [
                'aString',
                'a_string',
                S::LOWER,
            ],
            [
                'aString',
                'a-string',
                S::LOWER,
            ],
            [
                'AString',
                'a_string',
                S::UPPER,
            ],
            [
                'AString',
                'a-string',
                S::UPPER,
            ],
            
            [
                'some4Numbers234',
                'some4_numbers234',
                S::LOWER,
            ],
            [
                'some4Numbers234',
                'some4-numbers234',
                S::LOWER,
            ],
            [
                'Some4Numbers234',
                'some4_numbers234',
                S::UPPER,
            ],
            [
                'Some4Numbers234',
                'some4-numbers234',
                S::UPPER,
            ],
        ];
    }
}

