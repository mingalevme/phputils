<?php
 
use Mingalevme\Utils\Sql;

class SqlTest extends \PHPUnit_Framework_TestCase
{
    public function testToArray()
    {
        $this->assertSame("ARRAY['foo','bar']", Sql::toArray(['foo', 'bar']));
        
        $this->assertSame("ARRAY[1,2]", Sql::toArray([1, 2]));
        
        $this->assertSame("ARRAY[1,2]", Sql::toArray(['1', '2']));
        
        $this->assertSame("ARRAY['foo\\\"bar','bar\'foo']", Sql::toArray(['foo"bar', 'bar\'foo'], function($str){
            return addslashes($str);
        }));
        
        $this->assertSame("ARRAY[ARRAY['foo\\\"bar'],ARRAY['bar\'foo']]", Sql::toArray([['foo"bar'], ['bar\'foo']], function($str){
            return addslashes($str);
        }));
    }
}
