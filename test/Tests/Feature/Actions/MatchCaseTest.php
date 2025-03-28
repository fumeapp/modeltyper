<?php

namespace Tests\Feature\Actions;

use FumeApp\ModelTyper\Actions\MatchCase;
use Tests\TestCase;

class MatchCaseTest extends TestCase
{
    public function test_snake_case()
    {
        $value = 'TestValue';
        $result = app(MatchCase::class)('snake', $value);
        $this->assertEquals('test_value', $result);
    }

    public function test_camel_case()
    {
        $value = 'test_value';
        $result = app(MatchCase::class)('camel', $value);
        $this->assertEquals('testValue', $result);
    }

    public function test_pascal_case()
    {
        $value = 'test_value';
        $result = app(MatchCase::class)('pascal', $value);
        $this->assertEquals('TestValue', $result);
    }

    public function test_default_case()
    {
        $value = 'TestValue';
        $result = app(MatchCase::class)('', $value);
        $this->assertEquals($value, $result);
    }
}
