<?php

namespace Tests\Feature\Actions;

use FumeApp\ModelTyper\Actions\BuildModelDetails;
use Tests\TestCase;

class BuildModelDetailsTest extends TestCase
{
    public function test_action_can_be_resolved_by_application()
    {
        $this->assertInstanceOf(BuildModelDetails::class, resolve(BuildModelDetails::class));
    }

    public function test_action_can_be_executed()
    {
        $this->markTestIncomplete();
    }
}
