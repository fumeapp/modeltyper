<?php

namespace Tests\Feature\Actions;

use FumeApp\ModelTyper\Actions\BuildModelDetails;
use Tests\Feature\TestCase;

class BuildModelDetailsTest extends TestCase
{
    /** @test */
    public function test_action_can_be_resolved_by_application()
    {
        $this->assertInstanceOf(BuildModelDetails::class, resolve(BuildModelDetails::class));
    }

    /** @test */
    public function test_action_can_be_executed()
    {
        // TODO
        $this->assertTrue(true);
    }
}
