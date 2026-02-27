<?php

namespace Tests\Feature\Actions;

use App\Models\User;
use FumeApp\ModelTyper\Actions\WriteColumnAttribute;
use ReflectionClass;
use Tests\TestCase;

class WriteColumnAttributeTest extends TestCase
{
    public array $attribute = [
        'name' => 'role_traditional',
        'type' => null,
        'increments' => false,
        'nullable' => null,
        'default' => null,
        'unique' => null,
        'fillable' => true,
        'hidden' => false,
        'appended' => false,
        'cast' => 'accessor',
    ];

    public array $mappings = [
        'array' => 'string[]',
        'bigint' => 'number',
        'bool' => 'boolean',
        'boolean' => 'boolean',
        'float' => 'number',
        'int' => 'number',
        'string' => 'string',
    ];

    public function test_action_can_be_resolved_by_application()
    {
        $this->assertInstanceOf(WriteColumnAttribute::class, resolve(WriteColumnAttribute::class));
    }

    public function test_action_can_be_executed()
    {
        $action = app(WriteColumnAttribute::class);
        $result = $action(new ReflectionClass(User::class), $this->attribute, $this->mappings);

        $this->assertIsArray($result);

        $this->assertArrayHasKey('0', $result);
        $this->assertArrayHasKey('1', $result);
    }

    public function test_union_type_float_int_maps_to_number()
    {
        $action = app(WriteColumnAttribute::class);
        $attribute = array_merge($this->attribute, ['name' => 'score']);
        $result = $action(new ReflectionClass(User::class), $attribute, $this->mappings, jsonOutput: true);

        $this->assertIsArray($result);
        $this->assertEquals('number', $result[0]['type']);
        $this->assertNull($result[1]);
    }

    public function test_nullable_union_type_float_int_null()
    {
        $action = app(WriteColumnAttribute::class);
        $attribute = array_merge($this->attribute, ['name' => 'score_nullable']);
        $result = $action(new ReflectionClass(User::class), $attribute, $this->mappings, jsonOutput: true);

        $this->assertIsArray($result);
        $this->assertEquals('number | null', $result[0]['type']);
    }

    public function test_union_type_with_enum()
    {
        $action = app(WriteColumnAttribute::class);
        $attribute = array_merge($this->attribute, ['name' => 'role_or_string']);
        $result = $action(new ReflectionClass(User::class), $attribute, $this->mappings, jsonOutput: true);

        $this->assertIsArray($result);
        $this->assertEquals('Roles | string', $result[0]['type']);
        $this->assertNotNull($result[1]);
    }
}
