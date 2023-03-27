<?php

namespace Tests\Feature\Console;

use FumeApp\ModelTyper\Commands\ShowModelCommand;
use Tests\Feature\TestCase;

class ShowModelCommandTest extends TestCase
{
    public function testCommandFailsWhenTryingToUseAbstractModel()
    {
        $this->artisan(ShowModelCommand::class, ['model' => AbstractModel::class])->assertFailed();
    }
}
