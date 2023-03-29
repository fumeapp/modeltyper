<?php

namespace Tests\Feature\Actions;

use FumeApp\ModelTyper\Actions\GenerateJsonOutput;
use FumeApp\ModelTyper\Actions\GetModels;
use Tests\Feature\TestCase;

class GenerateJsonOutputTest extends TestCase
{
    public function testActionCanBeResolvedByApplication()
    {
        $this->assertInstanceOf(GenerateJsonOutput::class, resolve(GenerateJsonOutput::class));
    }

    public function testActionCanBeExecuted()
    {
        $action = new GenerateJsonOutput;
        $result = $action(app(GetModels::class)());

        $this->assertIsString($result);
    }
}
