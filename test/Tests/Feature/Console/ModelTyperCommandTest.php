<?php

namespace Tests\Feature\Console;

use App\Models\Complex;
use App\Models\User;
use FumeApp\ModelTyper\Commands\ModelTyperCommand;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use Tests\Traits\GeneratesOutput;
use Tests\Traits\UsesInputFiles;

class ModelTyperCommandTest extends TestCase
{
    use GeneratesOutput, RefreshDatabase, UsesInputFiles;

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->deleteOutput();
    }

    public function test_command_can_be_executed_successfully()
    {
        $this->artisan(ModelTyperCommand::class)->assertSuccessful();
    }

    public function test_command_fails_when_trying_to_resolve_abstract_model_that_has_no_binding()
    {
        $this->markTestSkipped('Do dont think is needed anymore, since only files that extend Eloquent\Model are considered');

        // ignoring for now
        $this->artisan(ModelTyperCommand::class, ['--resolve-abstract' => true])->assertFailed();
    }

    public function test_command_generates_expected_output_for_user_model()
    {
        $expected = $this->getExpectedContent('example.ts');
        $this->artisan(ModelTyperCommand::class, ['--model' => User::class])->expectsOutput($expected);
    }

    public function test_command_generates_expected_output_for_user_model_when_output_file_argument_is_set()
    {
        $expected = $this->getExpectedContent('example.ts');

        $this->artisan(ModelTyperCommand::class, ['output-file' => './test/output/models.d.ts', '--model' => User::class])
            ->expectsOutput('Typescript interfaces generated in ./test/output/models.d.ts file');

        $actual = $this->getGeneratedFileContents('models.d.ts');

        $this->assertSame($expected, $actual);
    }

    public function test_command_generates_fillables_when_fillable_option_is_enabled()
    {
        $expected = $this->getExpectedContent('user-fillables.ts');
        $options = [
            '--model' => User::class,
            '--fillables' => true,
            '--fillable-suffix' => 'Editable',
        ];

        $this->artisan(ModelTyperCommand::class, $options)->expectsOutput($expected);
    }

    public function test_command_generates_expected_output_for_complex_model()
    {
        // assert table complex_model_table exists
        $this->assertDatabaseEmpty('complex_model_table');

        // check if Complex::class generates expected interface
        $expected = $this->getExpectedContent('complex-model.ts');
        $this->artisan(ModelTyperCommand::class, ['--model' => Complex::class])->expectsOutput($expected);
    }

    public function test_command_generates_expected_output_for_complex_model_when_user_types_unknown_custom_cast()
    {
        // set UpperCast return type in config
        Config::set('modeltyper.custom_mappings', [
            'App\Casts\UpperCast' => 'string',
        ]);

        // assert table complex_model_table exists
        $this->assertDatabaseEmpty('complex_model_table');

        // check if Complex::class generates expected interface
        $expected = $this->getExpectedContent('complex-model-with-cast.ts');
        $this->artisan(ModelTyperCommand::class, ['--model' => Complex::class])->expectsOutput($expected);
    }
}
