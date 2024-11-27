<?php

namespace Tests\Feature\Actions;

use FumeApp\ModelTyper\Actions\Generator;
use Tests\Feature\TestCase;

class GeneratorTest extends TestCase
{
    /** @test */
    public function test_action_can_be_resolved_by_application()
    {
        $this->assertInstanceOf(Generator::class, resolve(Generator::class));
    }

    /** @test */
    public function test_action_can_be_executed()
    {
        // TODO
        $this->assertTrue(true);
    }
}
