<?php

namespace Tests\Feature\Actions;

use FumeApp\ModelTyper\Actions\WriteExist;
use Tests\TestCase;

class WriteExistTest extends TestCase
{
    protected array $relation = [
        'name' => 'notifications',
        'type' => 'HasMany',
        'related' => "Illuminate\Notifications\DatabaseNotification",
    ];

    public function test_action_can_be_resolved_by_application()
    {
        $this->assertInstanceOf(WriteExist::class, resolve(WriteExist::class));
    }

    public function test_action_can_be_executed()
    {
        $action = app(WriteExist::class);
        $result = $action($this->relation);

        $this->assertIsString($result);
        $this->assertStringContainsString('notifications_exists: boolean', $result);
    }

    public function test_action_can_return_array()
    {
        $action = app(WriteExist::class);
        $result = $action($this->relation, jsonOutput: true);

        $this->assertIsArray($result);
        $this->assertEquals([
            'name' => 'notifications_exists',
            'type' => 'boolean',
        ], $result);
    }

    public function test_action_can_be_indented()
    {
        $action = app(WriteExist::class);
        $result = $action($this->relation, indent: 'ASDF');

        $this->assertStringContainsString('ASDF  notifications_exists: boolean', $result);
    }

    public function test_action_can_return_optional_exists()
    {
        $action = app(WriteExist::class);
        $result = $action($this->relation, optionalExists: true);

        $this->assertStringContainsString('notifications_exists?: boolean', $result);
    }

    public function test_action_can_return_optional_exists_with_pascal_case()
    {
        config(['modeltyper.case.columns' => 'pascal']);
        $action = app(WriteExist::class);
        $result = $action($this->relation, optionalExists: true);

        $this->assertStringContainsString('NotificationsExists?: boolean', $result);
    }

    public function test_action_can_return_optional_exists_with_camel_case()
    {
        config(['modeltyper.case.columns' => 'camel']);
        $action = app(WriteExist::class);
        $result = $action($this->relation, optionalExists: true);

        $this->assertStringContainsString('notificationsExists?: boolean', $result);
    }

    public function test_action_can_return_optional_exists_as_array()
    {
        $action = app(WriteExist::class);
        $result = $action($this->relation, jsonOutput: true, optionalExists: true);

        $this->assertEquals([
            'name' => 'notifications_exists?',
            'type' => 'boolean',
        ], $result);
    }

    public function test_action_can_return_optional_exists_as_array_with_pascal_case()
    {
        config(['modeltyper.case.columns' => 'pascal']);
        $action = app(WriteExist::class);
        $result = $action($this->relation, jsonOutput: true, optionalExists: true);

        $this->assertEquals([
            'name' => 'NotificationsExists?',
            'type' => 'boolean',
        ], $result);
    }

    public function test_action_can_return_optional_exists_as_array_with_camel_case()
    {
        config(['modeltyper.case.columns' => 'camel']);
        $action = app(WriteExist::class);
        $result = $action($this->relation, jsonOutput: true, optionalExists: true);

        $this->assertEquals([
            'name' => 'notificationsExists?',
            'type' => 'boolean',
        ], $result);
    }

    public function test_action_returns_empty_string_for_non_existable_relation()
    {
        $relation = [
            'name' => 'users',
            'type' => 'MorphedByMany',
            'related' => 'App\Models\User',
        ];

        $action = app(WriteExist::class);
        $result = $action($relation);

        $this->assertSame('', $result);
    }
}
