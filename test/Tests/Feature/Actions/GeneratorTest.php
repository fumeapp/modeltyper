<?php

namespace Tests\Feature\Actions;

use FumeApp\ModelTyper\Actions\Generator;
use Tests\TestCase;

class GeneratorTest extends TestCase
{
    public function test_action_can_be_resolved_by_application()
    {
        $this->assertInstanceOf(Generator::class, resolve(Generator::class));
    }

    public function test_action_can_be_executed()
    {
        $this->markTestIncomplete();
    }
}
