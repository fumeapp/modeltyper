<?php

namespace Tests\Feature\Actions;

use FumeApp\ModelTyper\Actions\GenerateCliOutput;
use FumeApp\ModelTyper\Actions\GetModels;
use Tests\Feature\TestCase;

class GenerateCliOutputTest extends TestCase
{
    public function testActionCanBeResolvedByApplication()
    {
        $this->assertInstanceOf(GenerateCliOutput::class, resolve(GenerateCliOutput::class));
    }

    public function testActionCanBeExecuted()
    {
        $action = new GenerateCliOutput;
        $result = $action(app(GetModels::class)());

        $this->assertIsString($result);
    }
}
