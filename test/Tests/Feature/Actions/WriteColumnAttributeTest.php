<?php

namespace Tests\Feature\Actions;

use FumeApp\ModelTyper\Actions\WriteColumnAttribute;
use Tests\Feature\TestCase;

class WriteColumnAttributeTest extends TestCase
{
    /** @test */
    public function testActionCanBeResolvedByApplication()
    {
        $this->assertInstanceOf(WriteColumnAttribute::class, resolve(WriteColumnAttribute::class));
    }

    /** @test */
    public function testActionCanBeExecuted()
    {
        // TODO
        $this->assertTrue(true);
    }
}
