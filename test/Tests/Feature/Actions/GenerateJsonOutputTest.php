<?php

namespace Tests\Feature\Actions;

use FumeApp\ModelTyper\Actions\GenerateJsonOutput;
use FumeApp\ModelTyper\Actions\GetModels;
use Tests\Feature\TestCase;

class GenerateJsonOutputTest extends TestCase
{
    /** @test */
    public function testActionCanBeResolvedByApplication()
    {
        $this->assertInstanceOf(GenerateJsonOutput::class, resolve(GenerateJsonOutput::class));
    }

    /** @test */
    public function testActionCanBeExecuted()
    {
        $action = app(GenerateJsonOutput::class);
        $result = $action(app(GetModels::class)());

        $this->assertIsString($result);
    }
}
