<?php

namespace Tests\Feature\Actions;

use FumeApp\ModelTyper\Actions\WriteColumnAttribute;
use Tests\TestCase;

class WriteColumnAttributeTest extends TestCase
{
    public function test_action_can_be_resolved_by_application()
    {
        $this->assertInstanceOf(WriteColumnAttribute::class, resolve(WriteColumnAttribute::class));
    }

    public function test_action_can_be_executed()
    {
        $this->markTestIncomplete();
    }
}
