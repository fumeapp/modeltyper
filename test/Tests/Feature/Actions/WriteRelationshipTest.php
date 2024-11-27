<?php

namespace Tests\Feature\Actions;

use FumeApp\ModelTyper\Actions\WriteRelationship;
use Tests\TestCase;

class WriteRelationshipTest extends TestCase
{
    public function test_action_can_be_resolved_by_application()
    {
        $this->assertInstanceOf(WriteRelationship::class, resolve(WriteRelationship::class));
    }

    public function test_action_can_be_executed()
    {
        $this->markTestIncomplete();
    }
}
