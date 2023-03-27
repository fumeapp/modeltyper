<?php

namespace Tests\Feature\Actions;

use App\Models\AbstractModel;
use App\Models\User;
use FumeApp\ModelTyper\Actions\RunModelShowCommand;
use FumeApp\ModelTyper\Exceptions\NestedCommandException;
use Tests\Feature\TestCase;

class RunShowModelCommandTest extends TestCase
{
    public function testActionCanBeResolvedByApplication()
    {
        $this->assertInstanceOf(RunModelShowCommand::class, resolve(RunModelShowCommand::class));
    }

    public function testActionCanBeExecuted()
    {
        $action = new RunModelShowCommand;
        $result = $action(User::class);

        $this->assertNotEmpty($result);
    }

    public function testTryingToExecuteActionWithAnAbsractModelResultsInException()
    {
        $action = new RunModelShowCommand;

        $this->expectException(NestedCommandException::class);
        $result = $action(AbstractModel::class);
    }
}
