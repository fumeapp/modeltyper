<?php

namespace Tests\Feature\Console;

use FumeApp\ModelTyper\Commands\ModelTyperCommand;
use Tests\Feature\TestCase;
use Tests\Traits\GeneratesOutput;

class ModelTyperCommandTest extends TestCase
{
    use GeneratesOutput;

    protected function tearDown() : void
    {
        parent::tearDown();
        $this->deleteOutput();
    }

    public function testBaseCommandCanBeExecutedSuccessfully()
    {
        $this->artisan(ModelTyperCommand::class)->assertSuccessful();
    }

    public function testBaseCommandFailsWhenTryingToResolveAbstractModelThatHasNoBinding()
    {
        $this->artisan(ModelTyperCommand::class, ['--resolve-abstract' => true])->assertFailed();
    }
}
