<?php

namespace Tests\Feature\Console;

use App\Models\AbstractModel;
use FumeApp\ModelTyper\Commands\ShowModelCommand;
use Tests\Feature\TestCase;

class ShowModelCommandTest extends TestCase
{
    /** @test */
    public function testCommandFailsWhenTryingToUseAbstractModel()
    {
        $this->artisan(ShowModelCommand::class, ['model' => AbstractModel::class])->assertFailed();
    }
}
