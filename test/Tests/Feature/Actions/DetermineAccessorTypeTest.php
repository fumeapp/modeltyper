<?php

namespace Tests\Feature\Actions;

use FumeApp\ModelTyper\Actions\DetermineAccessorType;
use Tests\Feature\TestCase;
use Tests\Traits\ResolveClassAsReflection;

class DetermineAccessorTypeTest extends TestCase
{
    use ResolveClassAsReflection;

    /** @test */
    public function test_action_can_be_resolved_by_application()
    {
        $this->assertInstanceOf(DetermineAccessorType::class, resolve(DetermineAccessorType::class));
    }

    /** @test */
    public function test_action_can_be_executed()
    {
        // TODO
        $this->assertTrue(true);
    }
}
