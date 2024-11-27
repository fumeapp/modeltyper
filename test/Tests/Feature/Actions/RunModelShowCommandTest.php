<?php

namespace Tests\Feature\Actions;

use App\Models\AbstractModel;
use App\Models\User;
use FumeApp\ModelTyper\Actions\RunModelShowCommand;
use FumeApp\ModelTyper\Exceptions\NestedCommandException;
use Tests\Feature\TestCase;

class RunModelShowCommandTest extends TestCase
{
    /** @test */
    public function test_action_can_be_resolved_by_application()
    {
        $this->assertInstanceOf(RunModelShowCommand::class, resolve(RunModelShowCommand::class));
    }

    /** @test */
    public function test_action_can_be_executed()
    {
        $action = app(RunModelShowCommand::class);
        $result = $action(User::class);

        $this->assertNotEmpty($result);
    }

    /** @test */
    public function test_trying_to_execute_action_with_an_absract_model_results_in_exception()
    {
        $action = app(RunModelShowCommand::class);

        $this->expectException(NestedCommandException::class);
        $result = $action(AbstractModel::class);
    }
}
