<?php
 
use Mingalevme\Utils\HTTP;
 
class HTTPTest extends PHPUnit_Framework_TestCase
{
    public function testBuildUrl()
    {
        $url = 'http://github.com/mingalevme/utils';
        
        $this->assertTrue(HTTP::buildUrl($url, array(
            'S' => 'https',
            'PATH' => '/mingalevme/utils/blob/master/README.md',
            'f' => 'fragment'
        )) === 'https://github.com/mingalevme/utils');
    }
}
