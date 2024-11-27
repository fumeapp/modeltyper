<?php

namespace Tests\Feature\Actions;

use FumeApp\ModelTyper\Actions\WriteRelationship;
use Tests\Feature\TestCase;

class WriteRelationshipTest extends TestCase
{
    /** @test */
    public function test_action_can_be_resolved_by_application()
    {
        $this->assertInstanceOf(WriteRelationship::class, resolve(WriteRelationship::class));
    }

    /** @test */
    public function test_action_can_be_executed()
    {
        // TODO
        $this->assertTrue(true);
    }
}
