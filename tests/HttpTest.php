<?php

namespace Mingalevme\Tests\Utils;

use Mingalevme\Utils\Http;

class HttpTest extends TestCase
{
    public function testParseHeadser()
    {
        $string = "HTTP/1.1 401 Unauthorized\r\nServer: nginx/1.10.3\r\nDate: Thu, 27 Apr 2017 08:48:51 GMT\r\nContent-Type: text/html\r\nWWW-Authenticate: Basic realm=\"Forbidden\"\r\n\r\n";
        $headers = Http::parseHeaders($string, $statusCode, $statusLine);
        $this->assertEquals(401, $statusCode);
        $this->assertEquals('HTTP/1.1 401 Unauthorized', $statusLine);
        $this->assertEquals($headers['Server'], 'nginx/1.10.3');
        $this->assertEquals($headers['Date'], 'Thu, 27 Apr 2017 08:48:51 GMT');
        $this->assertEquals($headers['Content-Type'], 'text/html');
        $this->assertEquals($headers['WWW-Authenticate'], 'Basic realm="Forbidden"');
    }
}
