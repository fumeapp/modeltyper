<?php

namespace Tests\Feature\Console;

use App\Models\AbstractModel;
use FumeApp\ModelTyper\Commands\ShowModelCommand;
use Tests\TestCase;

class ShowModelCommandTest extends TestCase
{
    public function test_command_fails_when_trying_to_use_abstract_model()
    {
        $this->artisan(ShowModelCommand::class, ['model' => AbstractModel::class])->assertFailed();
    }
}
