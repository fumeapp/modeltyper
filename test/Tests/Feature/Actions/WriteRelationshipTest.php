<?php

namespace Tests\Feature\Actions;

use FumeApp\ModelTyper\Actions\WriteRelationship;
use Tests\Feature\TestCase;

class WriteRelationshipTest extends TestCase
{
    /** @test */
    public function testActionCanBeResolvedByApplication()
    {
        $this->assertInstanceOf(WriteRelationship::class, resolve(WriteRelationship::class));
    }

    /** @test */
    public function testActionCanBeExecuted()
    {
        // TODO
        $this->assertTrue(true);
    }
}
