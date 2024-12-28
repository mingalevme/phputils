<?php

declare(strict_types=1);

namespace Mingalevme\Tests\Utils;

use Mingalevme\Utils\Json as J;
use Mingalevme\Utils\Json\Exception\ParseException;

/**
 * http://blog.nikolaposa.in.rs/2017/02/13/testing-conventions/
 */
final class JsonTest extends TestCase
{
    /**
     * @test
     */
    public function it_decodes_valid_json(): void
    {
        $this->assertEquals([
            'foo1' => 'bar1',
            'foo2' => 'bar2',
        ], J::d('{"foo1":"bar1","foo2":"bar2"}'));
    }
    
    /**
     * @test
     */
    public function it_cannot_be_decoded_if_json_has_invalid_syntax(): void
    {
        try {
            J::d('Invalid-json-string');
            $this->fail(ParseException::class . ' should have been raised');
        } catch (ParseException $e) {
            $this->assertSame('Syntax error', $e->getMessage());
        }
    }
    
    /**
     * @test
     */
    public function it_cannot_be_decoded_if_json_has_state_mismatch(): void
    {
        try {
            J::d('{"foo": {]');
            $this->fail(ParseException::class . ' should have been raised');
        } catch (ParseException $e) {
            $this->assertSame('State mismatch (invalid or malformed JSON)', $e->getMessage());
        }
    }
    
    /**
     * @test
     */
    public function it_cannot_be_decoded_if_json_has_malformed_utf8_characters(): void
    {
        $str = \iconv('UTF8', 'CP1251', 'Тест');
        
        try {
            J::d("[\"{$str}\"]");
            $this->fail(ParseException::class . ' should have been raised');
        } catch (ParseException $e) {
            $this->assertSame('Malformed UTF-8 characters, possibly incorrectly encoded', $e->getMessage());
        }
    }
}
