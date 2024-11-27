<?php

namespace Tests\Feature\Actions;

use FumeApp\ModelTyper\Actions\WriteColumnAttribute;
use Tests\Feature\TestCase;

class WriteColumnAttributeTest extends TestCase
{
    /** @test */
    public function test_action_can_be_resolved_by_application()
    {
        $this->assertInstanceOf(WriteColumnAttribute::class, resolve(WriteColumnAttribute::class));
    }

    /** @test */
    public function test_action_can_be_executed()
    {
        // TODO
        $this->assertTrue(true);
    }
}
