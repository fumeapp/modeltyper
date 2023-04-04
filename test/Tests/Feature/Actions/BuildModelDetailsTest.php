<?php

namespace Tests\Feature\Actions;

use FumeApp\ModelTyper\Actions\BuildModelDetails;
use Tests\Feature\TestCase;

class BuildModelDetailsTest extends TestCase
{
    /** @test */
    public function testActionCanBeResolvedByApplication()
    {
        $this->assertInstanceOf(BuildModelDetails::class, resolve(BuildModelDetails::class));
    }

    /** @test */
    public function testActionCanBeExecuted()
    {
        // TODO
        $this->assertTrue(true);
    }
}
