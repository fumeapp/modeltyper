<?php

namespace Tests\Feature\Console;

use Illuminate\Support\Facades\Artisan;
use Tests\Traits\GeneratesOutput;

class ModelTyperCommandTest extends ConsoleTestCase
{
    use GeneratesOutput;

    protected function tearDown() : void
    {
        parent::tearDown();
        $this->deleteOutput();
    }

    public function testFoo()
    {
        // $foo = $this->artisan('model:typer');
        $this->assertTrue(true);
    }
}
