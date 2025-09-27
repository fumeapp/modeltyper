<?php

namespace Tests\Feature\Actions;

use ErrorException;
use FumeApp\ModelTyper\Actions\MapReturnType;
use Tests\TestCase;

class MapReturnTypeTest extends TestCase
{
    public function test_action_can_be_resolved_by_application()
    {
        $this->assertInstanceOf(MapReturnType::class, resolve(MapReturnType::class));
    }

    public function test_action_can_be_executed()
    {
        $action = app(MapReturnType::class);
        $this->assertIsString($action('A', []));
    }

    public function test_action_can_return_correct_type()
    {
        $action = app(MapReturnType::class);
        $this->assertEquals('2', $action('B', ['a' => '1', 'b' => '2']));
    }

    public function test_action_can_return_correct_nullable_type()
    {
        $action = app(MapReturnType::class);
        $this->assertEquals('1 | null', $action('?A', ['a' => '1', 'b' => '2']));
    }

    public function test_action_can_return_unknown_type()
    {
        $action = app(MapReturnType::class);
        $this->assertEquals('unknown', $action('C', ['a' => '1', 'b' => '2']));
    }

    public function test_action_throws_exception_empty_string()
    {
        $this->expectException(ErrorException::class);
        $this->expectExceptionMessage('Empty string');

        $action = app(MapReturnType::class);
        $this->assertIsString($action('', []));
    }
}
