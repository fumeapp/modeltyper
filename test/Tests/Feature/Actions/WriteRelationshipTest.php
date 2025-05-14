<?php

namespace Tests\Feature\Actions;

use FumeApp\ModelTyper\Actions\WriteRelationship;
use Illuminate\Support\Facades\Config;
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
        $this->assertEquals([
            'name' => 'notifications',
            'type' => 'DatabaseNotification[]',
            'count' => [
                'name' => 'notifications_count',
                'type' => 'number',
            ],
        ], $result);
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

        $this->assertEquals([
            'name' => 'notifications?',
            'type' => 'DatabaseNotification[]',
            'count' => [
                'name' => 'notifications_count',
                'type' => 'number',
            ],
        ], $result);
    }

    public function test_action_can_return_plural_relationships()
    {
        $action = app(WriteRelationship::class);
        $result = $action(relation: $this->relation, plurals: true);

        $this->assertStringContainsString('notifications: DatabaseNotifications', $result);
    }

    public function test_action_can_handle_no_counts_option()
    {
        Config::set('modeltyper.no-counts', true);
        $action = app(WriteRelationship::class);
        $result = $action($this->relation);

        $this->assertStringContainsString('notifications: DatabaseNotification', $result);
        $this->assertStringNotContainsString('notifications: DatabaseNotification[]', $result);
    }

    public function test_action_can_handle_optional_counts_option()
    {
        Config::set('modeltyper.optional-counts', true);
        $action = app(WriteRelationship::class);
        $result = $action($this->relation);

        $this->assertStringContainsString('notifications?: DatabaseNotification[]', $result);
    }

    public function test_action_prioritizes_no_counts_over_optional_counts()
    {
        Config::set('modeltyper.no-counts', true);
        Config::set('modeltyper.optional-counts', true);
        $action = app(WriteRelationship::class);
        $result = $action($this->relation);

        $this->assertStringContainsString('notifications: DatabaseNotification', $result);
        $this->assertStringNotContainsString('notifications?: DatabaseNotification[]', $result);
    }

    public function test_action_handles_non_countable_relations_with_no_counts()
    {
        Config::set('modeltyper.no-counts', true);
        $action = app(WriteRelationship::class);
        $nonCountableRelation = [
            'name' => 'profile',
            'type' => 'HasOne',
            'related' => "App\Models\Profile",
        ];
        $result = $action($nonCountableRelation);

        $this->assertStringContainsString('profile: Profile', $result);
    }

    public function test_action_adds_count_field_for_countable_relations()
    {
        $action = app(WriteRelationship::class);
        $result = $action($this->relation);

        $this->assertStringContainsString('notifications: DatabaseNotification[]', $result);
        $this->assertStringContainsString('notifications_count: number', $result);
    }

    public function test_action_adds_count_field_for_countable_relations_in_json_output()
    {
        $action = app(WriteRelationship::class);
        $result = $action(relation: $this->relation, jsonOutput: true);

        $this->assertIsArray($result);
        $this->assertEquals([
            'name' => 'notifications',
            'type' => 'DatabaseNotification[]',
            'count' => [
                'name' => 'notifications_count',
                'type' => 'number',
            ],
        ], $result);
    }

    public function test_action_does_not_add_count_field_for_non_countable_relations()
    {
        $action = app(WriteRelationship::class);
        $nonCountableRelation = [
            'name' => 'profile',
            'type' => 'HasOne',
            'related' => "App\Models\Profile",
        ];
        $result = $action($nonCountableRelation);

        $this->assertStringContainsString('profile: Profile', $result);
        $this->assertStringNotContainsString('profile_count', $result);
    }

    public function test_action_does_not_add_count_field_when_no_counts_is_enabled()
    {
        Config::set('modeltyper.no-counts', true);
        $action = app(WriteRelationship::class);
        $result = $action($this->relation);

        $this->assertStringContainsString('notifications: DatabaseNotification', $result);
        $this->assertStringNotContainsString('notifications_count', $result);
    }

    public function test_action_optional_counts_makes_count_field_optional()
    {
        Config::set('modeltyper.optional-counts', true);
        $action = app(WriteRelationship::class);
        $result = $action($this->relation);

        // The main relation should be optional
        $this->assertStringContainsString('notifications?: DatabaseNotification[]', $result);
        // The count field should still be required (since counts are always present if relation is loaded)
        $this->assertStringContainsString('notifications_count: number', $result);
    }
}
