<?php

namespace Tests\Feature\Actions;

use FumeApp\ModelTyper\Actions\WriteCount;
use FumeApp\ModelTyper\Actions\WriteExist;
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

        $this->assertEquals([
            'name' => 'notifications',
            'type' => 'DatabaseNotification[]',
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
        $result = $action(relation: $this->relation, jsonOutput: true, optionalRelation: true);

        $this->assertEquals([
            'name' => 'notifications?',
            'type' => 'DatabaseNotification[]',
        ], $result);
    }

    public function test_action_can_return_plural_relationships()
    {
        $action = app(WriteRelationship::class);
        $result = $action(relation: $this->relation, plurals: true);

        $this->assertStringContainsString('notifications: DatabaseNotifications', $result);
    }

    // Add newly added count and exists tests
    public function test_action_can_return_count_relationships()
    {
        $action = app(WriteCount::class);
        $result = $action(relation: $this->relation);

        $this->assertStringContainsString('notifications_count: number', $result);
    }

    public function test_action_can_return_exists_relationships()
    {
        $action = app(WriteExist::class);
        $result = $action(relation: $this->relation);

        $this->assertStringContainsString('notifications_exists: boolean', $result);
    }

    public function test_action_can_return_optional_count_relationships()
    {
        $action = app(WriteCount::class);
        $result = $action(relation: $this->relation, optionalCounts: true);

        $this->assertStringContainsString('notifications_count?: number', $result);
    }

    public function test_action_can_return_optional_exists_relationships()
    {
        $action = app(WriteExist::class);
        $result = $action(relation: $this->relation, optionalExists: true);

        $this->assertStringContainsString('notifications_exists?: boolean', $result);
    }

    public function test_action_can_return_nullable_relationships()
    {
        $nullableRelation = [
            'name' => 'listing',
            'type' => 'BelongsTo',
            'related' => 'App\Models\Listing',
            'nullable' => true,
        ];

        $action = app(WriteRelationship::class);
        $result = $action(relation: $nullableRelation);

        $this->assertStringContainsString('listing: Listing | null', $result);
    }

    public function test_action_can_return_nullable_relationships_as_array()
    {
        $nullableRelation = [
            'name' => 'listing',
            'type' => 'BelongsTo',
            'related' => 'App\Models\Listing',
            'nullable' => true,
        ];

        $action = app(WriteRelationship::class);
        $result = $action(relation: $nullableRelation, jsonOutput: true);

        $this->assertEquals([
            'name' => 'listing',
            'type' => 'Listing | null',
        ], $result);
    }

    public function test_action_can_return_non_nullable_relationships()
    {
        $nonNullableRelation = [
            'name' => 'user',
            'type' => 'BelongsTo',
            'related' => 'App\Models\User',
            'nullable' => false,
        ];

        $action = app(WriteRelationship::class);
        $result = $action(relation: $nonNullableRelation);

        $this->assertStringContainsString('user: User', $result);
        $this->assertStringNotContainsString('user?: User', $result);
        $this->assertStringNotContainsString('user: User | null', $result);
    }

    public function test_action_can_return_nullable_plural_relationships()
    {
        $nullableRelation = [
            'name' => 'tags',
            'type' => 'BelongsToMany',
            'related' => 'App\Models\Tag',
            'nullable' => true,
        ];

        $action = app(WriteRelationship::class);
        $result = $action(relation: $nullableRelation);

        $this->assertStringContainsString('tags: Tag[] | null', $result);
    }

    public function test_action_can_return_morph_to_union_type_relationships()
    {
        $morphToRelation = [
            'name' => 'model',
            'type' => 'MorphTo',
            'related' => 'User|Complex',
        ];

        $action = app(WriteRelationship::class);
        $result = $action(relation: $morphToRelation);

        $this->assertStringContainsString('model: User|Complex', $result);
    }

    public function test_action_can_return_morph_to_union_type_relationships_as_array()
    {
        $morphToRelation = [
            'name' => 'model',
            'type' => 'MorphTo',
            'related' => 'User|Complex',
        ];

        $action = app(WriteRelationship::class);
        $result = $action(relation: $morphToRelation, jsonOutput: true);

        $this->assertEquals([
            'name' => 'model',
            'type' => 'User|Complex',
        ], $result);
    }

    public function test_action_can_return_nullable_morph_to_union_type_relationships()
    {
        $morphToRelation = [
            'name' => 'model',
            'type' => 'MorphTo',
            'related' => 'User|Complex',
            'nullable' => true,
        ];

        $action = app(WriteRelationship::class);
        $result = $action(relation: $morphToRelation);

        $this->assertStringContainsString('model: User|Complex | null', $result);
    }
}
