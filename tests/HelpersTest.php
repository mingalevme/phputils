<?php

namespace Mingalevme\Tests\Utils;

class HelpersTest extends TestCase
{
    public function testUrlGetContents()
    {
        $this->assertSame('https://stackoverflow.com/questions/7229885/what-are-the-differences-between-gitignore-and-gitkeep',
            trim(url_get_contents('https://raw.githubusercontent.com/mingalevme/phputils/master/tests/.gitkeep?rand=1', $headers)));
        $this->assertSame('text/plain; charset=utf-8', $headers['Content-Type']);
        
        try {
            $this->assertSame('https://stackoverflow.com/questions/7229885/what-are-the-differences-between-gitignore-and-gitkeep',
                url_get_contents('https://raw.githubusercontent.com/mingalevme/phputils/master/tests/null'));
            $this->fail('ErrorException should have been raised');
        } catch (\ErrorException $e) {
            $this->assertInstanceOf(\ErrorException::class, $e);
        }
        
        try {
            $this->assertSame('https://stackoverflow.com/questions/7229885/what-are-the-differences-between-gitignore-and-gitkeep',
                url_get_contents(
                    'https://raw.githubusercontent.com/mingalevme/phputils/master/tests/null',
                    $null,
                    null,
                    2,
                    function ($attemp) { throw new \RuntimeException(); }
                )
            );
            $this->fail('RuntimeException should have been raised');
        } catch (\RuntimeException $e) {
            $this->assertInstanceOf(\RuntimeException::class, $e);
        }
    }
    
    public function testUrlGetContentsCustomUserResponseBody()
    {
        $url = 'https://raw.githubusercontent.com/mingalevme/phputils/master/tests/.foobar';
        
        $responseBody = url_get_contents($url, $headers, null, 1, function($attempt, $statusCode, $responseBody){
            return "url_get_contents/{$attempt}/{$statusCode}/" . trim($responseBody);
        });
        
        $this->assertSame('url_get_contents/1/404/404: Not Found', $responseBody);
    }

    public function testIsOddNumber()
    {
        $this->assertSame(false, is_odd_number(-2));
        $this->assertSame(true, is_odd_number(-1));
        $this->assertSame(false, is_odd_number(0));
        $this->assertSame(true, is_odd_number(1));
        $this->assertSame(false, is_odd_number(2));
    }

    public function testIsEvenNumber()
    {
        $this->assertSame(true, is_even_number(-2));
        $this->assertSame(false, is_even_number(-1));
        $this->assertSame(true, is_even_number(0));
        $this->assertSame(false, is_even_number(1));
        $this->assertSame(true, is_even_number(2));
    }
}
