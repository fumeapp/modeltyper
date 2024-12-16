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
}
