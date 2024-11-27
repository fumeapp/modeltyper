<?php

namespace Tests\Feature\Actions;

use FumeApp\ModelTyper\Actions\GenerateJsonOutput;
use FumeApp\ModelTyper\Actions\GetMappings;
use FumeApp\ModelTyper\Actions\GetModels;
use Tests\Feature\TestCase;

class GenerateJsonOutputTest extends TestCase
{
    /** @test */
    public function test_action_can_be_resolved_by_application()
    {
        $this->assertInstanceOf(GenerateJsonOutput::class, resolve(GenerateJsonOutput::class));
    }

    /** @test */
    public function test_action_can_be_executed()
    {
        $action = app(GenerateJsonOutput::class);
        $result = $action(app(GetModels::class)(), app(GetMappings::class)());

        $this->assertIsString($result);
    }
}
