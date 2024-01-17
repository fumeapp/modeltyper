<?php

namespace Tests\Feature\Console;

use App\Models\User;
use FumeApp\ModelTyper\Commands\ModelTyperCommand;
use Tests\Feature\TestCase;
use Tests\Traits\GeneratesOutput;
use Tests\Traits\UsesInputFiles;

class ModelTyperCommandTest extends TestCase
{
    use GeneratesOutput, UsesInputFiles;

    protected function tearDown(): void
    {
        parent::tearDown();

        // NOTE Not really necessary at the moment, but might be useful in the future
        // if something like --outputfile option is added to the command
        $this->deleteOutput();
    }

    /** @test */
    public function testCommandCanBeExecutedSuccessfully()
    {
        $this->artisan(ModelTyperCommand::class)->assertSuccessful();
    }

    /** @test */
    public function testCommandFailsWhenTryingToResolveAbstractModelThatHasNoBinding()
    {
        $this->markTestSkipped('Do dont think is needed anymore, since only files that extend Eloquent\Model are considered');

        // ignoring for now
        $this->artisan(ModelTyperCommand::class, ['--resolve-abstract' => true])->assertFailed();
    }

    /** @test */
    public function testCommandGeneratesExpectedOutputForUserModel()
    {
        $expected = $this->getExpectedContent('example.ts');
        $this->artisan(ModelTyperCommand::class, ['--model' => User::class])->expectsOutput($expected);
    }

    public function testCommandGeneratesFillablesWhenFillableOptionIsEnabled()
    {
        $expected = $this->getExpectedContent('user-fillables.ts');
        $options = [
            '--model' => User::class,
            '--fillables' => true,
            '--fillable-suffix' => 'Editable'
        ];

        $this->artisan(ModelTyperCommand::class, $options)->expectsOutput($expected);
    }
}
