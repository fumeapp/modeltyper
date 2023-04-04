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
    public function testActionCanBeResolvedByApplication()
    {
        $this->assertInstanceOf(RunModelShowCommand::class, resolve(RunModelShowCommand::class));
    }

    /** @test */
    public function testActionCanBeExecuted()
    {
        $action = app(RunModelShowCommand::class);
        $result = $action(User::class);

        $this->assertNotEmpty($result);
    }

    /** @test */
    public function testTryingToExecuteActionWithAnAbsractModelResultsInException()
    {
        $action = app(RunModelShowCommand::class);

        $this->expectException(NestedCommandException::class);
        $result = $action(AbstractModel::class);
    }
}
