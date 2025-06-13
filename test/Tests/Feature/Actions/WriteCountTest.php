<?php

namespace Tests\Feature\Actions;

use FumeApp\ModelTyper\Actions\WriteCount;
use Tests\TestCase;

class WriteCountTest extends TestCase
{
    protected array $relation = [
        'name' => 'notifications',
        'type' => 'HasMany',
        'related' => "Illuminate\Notifications\DatabaseNotification",
    ];

    public function test_action_can_be_resolved_by_application()
    {
        $this->assertInstanceOf(WriteCount::class, resolve(WriteCount::class));
    }

    public function test_action_can_be_executed()
    {
        $action = app(WriteCount::class);
        $result = $action($this->relation);

        $this->assertIsString($result);

        $this->assertStringContainsString('notifications_count: number', $result);
    }

    public function test_action_can_return_array()
    {
        $action = app(WriteCount::class);
        $result = $action($this->relation, jsonOutput: true);

        $this->assertIsArray($result);

        $this->assertEquals([
            'name' => 'notifications_count',
            'type' => 'number',
        ], $result);
    }

    public function test_action_can_be_indented()
    {
        $action = app(WriteCount::class);
        $result = $action($this->relation, indent: 'ASDF');

        $this->assertStringContainsString('ASDF  notifications_count: number', $result);
    }

    public function test_action_can_return_optional_counts()
    {
        $action = app(WriteCount::class);
        $result = $action($this->relation, optionalCounts: true);

        $this->assertStringContainsString('notifications_count?: number', $result);
    }

    public function test_action_can_return_optional_counts_with_pascal_case()
    {
        config(['modeltyper.case.columns' => 'pascal']);
        $action = app(WriteCount::class);
        $result = $action($this->relation, optionalCounts: true);

        $this->assertStringContainsString('NotificationsCount?: number', $result);
    }

    public function test_action_can_return_optional_counts_as_array()
    {
        $action = app(WriteCount::class);
        $result = $action($this->relation, jsonOutput: true, optionalCounts: true);

        $this->assertEquals([
            'name' => 'notifications_count?',
            'type' => 'number',
        ], $result);
    }

    public function test_action_returns_empty_string_for_non_countable_relation()
    {
        $relation = [
            'name' => 'user',
            'type' => 'BelongsTo',
            'related' => 'App\Models\User',
        ];

        $action = app(WriteCount::class);
        $result = $action($relation);

        $this->assertSame('', $result);
    }
}
