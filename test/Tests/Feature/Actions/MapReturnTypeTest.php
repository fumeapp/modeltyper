<?php

namespace Tests\Feature\Actions;

use FumeApp\ModelTyper\Actions\MapReturnType;
use Tests\Feature\TestCase;

class MapReturnTypeTest extends TestCase
{
    /** @test */
    public function testActionCanBeResolvedByApplication()
    {
        $this->assertInstanceOf(MapReturnType::class, resolve(MapReturnType::class));
    }

    /** @test */
    public function testActionCanBeExecuted()
    {
        // TODO
        $this->assertTrue(true);
    }
}
