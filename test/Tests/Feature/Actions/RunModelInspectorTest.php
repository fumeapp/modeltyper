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
        $action = app(RunModelInspector::class);
        $result = $action(User::class);

        $this->assertNotEmpty($result);
    }

    public function test_trying_to_execute_action_with_an_abstract_model_results_in_exception()
    {
        $this->markTestIncomplete();

        // $action = app(RunModelInspector::class);

        // $this->expectException(NestedCommandException::class);
        // $action(AbstractModel::class);
    }
}
