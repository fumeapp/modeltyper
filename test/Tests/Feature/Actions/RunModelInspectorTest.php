<?php

namespace Tests\Feature\Actions;

use App\Models\AbstractModel;
use App\Models\User;
use FumeApp\ModelTyper\Actions\RunModelInspector;
use Tests\TestCase;

class RunModelInspectorTest extends TestCase
{
    public function test_action_can_be_resolved_by_application()
    {
        $this->assertInstanceOf(RunModelInspector::class, resolve(RunModelInspector::class));
    }

    public function test_action_can_be_executed()
    {
        $result = app(RunModelInspector::class)(User::class);

        $this->assertIsArray($result);

        $this->assertArrayHasKey('class', $result);
        $this->assertArrayHasKey('database', $result);
        $this->assertArrayHasKey('table', $result);
        $this->assertArrayHasKey('policy', $result);
        $this->assertArrayHasKey('attributes', $result);
        $this->assertArrayHasKey('relations', $result);
        $this->assertArrayHasKey('events', $result);
        $this->assertArrayHasKey('observers', $result);
        $this->assertArrayHasKey('collection', $result);
        $this->assertArrayHasKey('builder', $result);
    }

    public function test_action_returns_null_on_abstract_model()
    {
        $result = app(RunModelInspector::class)(AbstractModel::class);

        $this->assertNull($result);
    }

    public function test_action_returns_null_on_non_existing_model()
    {
        $result = app(RunModelInspector::class)('NoExistsModel');

        $this->assertNull($result);
    }
}
