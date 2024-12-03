<?php

namespace Tests\Feature\Actions;

use FumeApp\ModelTyper\Actions\WriteRelationship;
use Tests\TestCase;

class WriteRelationshipTest extends TestCase
{
    protected array $relation = [
        'name' => 'notifications',
        'type' => 'MorphMany',
        'related' => "Illuminate\Notifications\DatabaseNotification",
    ];

    public function test_action_can_be_resolved_by_application()
    {
        $this->assertInstanceOf(WriteRelationship::class, resolve(WriteRelationship::class));
    }

    public function test_action_can_be_executed()
    {
        $action = app(WriteRelationship::class);
        $result = $action($this->relation);

        $this->assertIsString($result);

        $this->assertStringContainsString('notifications: DatabaseNotification[]', $result);
    }

    public function test_action_can_return_array()
    {
        $action = app(WriteRelationship::class);
        $result = $action(relation: $this->relation, jsonOutput: true);

        $this->assertIsArray($result);

        $this->assertEquals(['name' => 'notifications', 'type' => 'DatabaseNotification[]'], $result);
    }

    public function test_action_can_be_indented()
    {
        $action = app(WriteRelationship::class);
        $result = $action(relation: $this->relation, indent: 'ASDF');

        $this->assertStringContainsString('ASDF  notifications: DatabaseNotification[]', $result);
    }

    public function test_action_can_return_optional_relationships()
    {
        $action = app(WriteRelationship::class);
        $result = $action(relation: $this->relation, optionalRelation: true);

        $this->assertStringContainsString('notifications?: DatabaseNotification[]', $result);
    }

    public function test_action_can_return_optional_relationships_as_array()
    {
        $action = app(WriteRelationship::class);
        $result = $action(relation: $this->relation, optionalRelation: true, jsonOutput: true);

        $this->assertEquals(['name' => 'notifications?', 'type' => 'DatabaseNotification[]'], $result);
    }

    public function test_action_can_return_plural_relationships()
    {
        $action = app(WriteRelationship::class);
        $result = $action(relation: $this->relation, plurals: true);

        $this->assertStringContainsString('notifications: DatabaseNotifications', $result);
    }
}
