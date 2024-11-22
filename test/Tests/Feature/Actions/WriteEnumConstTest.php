<?php

namespace Tests\Feature\Actions;

use App\Enums\Roles;
use FumeApp\ModelTyper\Actions\WriteEnumConst;
use Tests\Feature\TestCase;
use Tests\Traits\GeneratesOutput;
use Tests\Traits\ResolveClassAsReflection;
use Tests\Traits\UsesInputFiles;

class WriteEnumConstTest extends TestCase
{
    use GeneratesOutput, ResolveClassAsReflection,UsesInputFiles;

    /** @test */
    public function test_action_can_be_resolved_by_application()
    {
        $this->assertInstanceOf(WriteEnumConst::class, resolve(WriteEnumConst::class));
    }

    /** @test */
    public function test_action_can_be_executed_and_returns_string()
    {
        $action = app(WriteEnumConst::class);
        $reflectionModel = $this->resolveClassAsReflection(Roles::class);

        $result = $action($reflectionModel);

        // expected output
        $expected = $this->getExpectedContent('enum.ts', true);

        $this->assertIsString($result);
        $this->assertEquals($expected, $result);
    }

    /** @test */
    public function test_action_can_be_executed_and_returns_array()
    {
        $action = app(WriteEnumConst::class);
        $reflectionModel = $this->resolveClassAsReflection(Roles::class);

        $result = $action(reflection: $reflectionModel, jsonOutput: true);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('type', $result);
        $this->assertEquals('Roles', $result['name']);
        $this->assertIsString($result['type']);
    }
}
