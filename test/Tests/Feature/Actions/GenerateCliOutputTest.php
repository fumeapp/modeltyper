<?php

namespace Tests\Feature\Actions;

use FumeApp\ModelTyper\Actions\GenerateCliOutput;
use FumeApp\ModelTyper\Actions\GetModels;
use Tests\Feature\TestCase;

class GenerateCliOutputTest extends TestCase
{
    /** @test */
    public function testActionCanBeResolvedByApplication()
    {
        $this->assertInstanceOf(GenerateCliOutput::class, resolve(GenerateCliOutput::class));
    }

    /** @test */
    public function testActionCanBeExecuted()
    {
        $action = app(GenerateCliOutput::class);
        $result = $action(app(GetModels::class)());

        $this->assertIsString($result);
    }
}
