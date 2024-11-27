<?php

namespace Tests\Feature\Actions;

use FumeApp\ModelTyper\Actions\MapReturnType;
use Tests\Feature\TestCase;

class MapReturnTypeTest extends TestCase
{
    /** @test */
    public function test_action_can_be_resolved_by_application()
    {
        $this->assertInstanceOf(MapReturnType::class, resolve(MapReturnType::class));
    }

    /** @test */
    public function test_action_can_be_executed()
    {
        // TODO
        $this->assertTrue(true);
    }
}
