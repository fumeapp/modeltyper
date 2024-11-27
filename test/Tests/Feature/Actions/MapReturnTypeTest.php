<?php

namespace Tests\Feature\Actions;

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
        $this->markTestIncomplete();
    }
}
