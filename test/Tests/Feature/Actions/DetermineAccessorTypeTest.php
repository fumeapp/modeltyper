<?php

namespace Tests\Feature\Actions;

use FumeApp\ModelTyper\Actions\DetermineAccessorType;
use Tests\Feature\TestCase;
use Tests\Traits\ResolveClassAsReflection;

class DetermineAccessorTypeTest extends TestCase
{
    use ResolveClassAsReflection;

    /** @test */
    public function testActionCanBeResolvedByApplication()
    {
        $this->assertInstanceOf(DetermineAccessorType::class, resolve(DetermineAccessorType::class));
    }

    /** @test */
    public function testActionCanBeExecuted()
    {
        // TODO
        $this->assertTrue(true);
    }
}
