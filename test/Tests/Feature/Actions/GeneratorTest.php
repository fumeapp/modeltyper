<?php

namespace Tests\Feature\Actions;

use FumeApp\ModelTyper\Actions\Generator;
use Tests\Feature\TestCase;

class GeneratorTest extends TestCase
{
    /** @test */
    public function testActionCanBeResolvedByApplication()
    {
        $this->assertInstanceOf(Generator::class, resolve(Generator::class));
    }

    /** @test */
    public function testActionCanBeExecuted()
    {
        // TODO
        $this->assertTrue(true);
    }
}
