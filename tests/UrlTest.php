<?php

use Mingalevme\Utils\Url;

class UrlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider buildDataProvider
     */
    public function testBuild($expected, $url, $params)
    {
        $this->assertEquals($expected, Url::build($url, $params));
    }
    
    public function buildDataProvider()
    {
        return [
            [
                'https://github.com/mingalevme/utils',
                '//github.com/mingalevme/utils', [
                    's' => 'https',
                ],
            ],
            [
                'https://github.com/mingalevme/utils',
                '/mingalevme/utils', [
                    's' => 'https',
                    'h' => 'github.com'
                ],
            ],
            [
                'https://github.com/mingalevme/utils',
                '//github.com/mingalevme/utils', [
                    's' => 'https',
                    'h' => 'bitbucket.com'
                ],
            ],
            [
                'https://github.com/mingalevme/utils',
                'http://bitbucket.com/mingalevme/utils', [
                    'S' => 'https',
                    'H' => 'github.com'
                ],
            ],
            [
                'https://username:password@github.com/mingalevme/utils',
                'https://github.com/mingalevme/utils', [
                    'user' => 'username',
                    'pass' => 'password'
                ],
            ],
            [
                'https://github.com/mingalevme/utils?foo=bar&bar=foo',
                'https://github.com/mingalevme/utils', [
                    'q' => 'foo=bar&bar=foo',
                ],
            ],
            [
                'https://github.com/mingalevme/utils?boo=far&far=boo&foo=bar&bar=foo',
                'https://github.com/mingalevme/utils?boo=far&far=boo', [
                    'q' => 'foo=bar&bar=foo',
                ],
            ],
            [
                'https://github.com/mingalevme/utils?foo=bar&bar=foo',
                'https://github.com/mingalevme/utils?boo=far&far=boo', [
                    'Q' => 'foo=bar&bar=foo',
                ],
            ],
            [
                'https://github.com/mingalevme/utils?boo=far&far=boo&foo=bar&bar=foo',
                'https://github.com/mingalevme/utils?boo=far&far=boo', [
                    'q' => [
                        'foo' => 'bar',
                        'bar' => 'foo',
                    ],
                ],
            ],
            [
                'https://github.com/mingalevme/utils?foo=bar&bar=foo',
                'https://github.com/mingalevme/utils?boo=far&far=boo', [
                    'Q' => [
                        'foo' => 'bar',
                        'bar' => 'foo',
                    ],
                ],
            ],
            [
                'https://github.com/mingalevme/utils#github',
                'http://bitbucket.com/mingalevme/utils/blob/master/README.md#bitbucket', [
                    'S' => 'https',
                    'H' => 'github.com',
                    'P' => '/mingalevme/utils',
                    'F' => 'github',
                ],
            ],
            
        ];
    }
}