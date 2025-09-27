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

    public function test_command_generates_expected_output_for_user_model()
    {
        $expected = $this->getExpectedContent('user.ts');
        $this->artisan(ModelTyperCommand::class, [
            '--model' => User::class,
        ])->expectsOutput($expected);
    }

    public function test_command_generates_expected_output_for_user_model_when_output_file_argument_is_set()
    {
        $expected = $this->getExpectedContent('user.ts');

        $this->artisan(ModelTyperCommand::class, [
            'output-file' => './test/output/models.d.ts',
            '--model' => User::class,
        ])
            ->expectsOutput('Typescript interfaces generated in ./test/output/models.d.ts file');

        $actual = $this->getGeneratedFileContents('models.d.ts');

        $this->assertSame($expected, $actual);
    }

    public function test_command_generates_fillables_when_option_is_enabled()
    {
        $expected = $this->getExpectedContent('user-fillables.ts');

        $this->artisan(ModelTyperCommand::class, [
            '--model' => User::class,
            '--fillables' => true,
            '--fillable-suffix' => 'Editable',
        ])->expectsOutput($expected);
    }

    public function test_command_generates_global_when_option_is_enabled()
    {
        // set global-namespace config
        Config::set('modeltyper.global-namespace', 'App.Models');

        $expected = $this->getExpectedContent('user-global.ts');

        $this->artisan(ModelTyperCommand::class, [
            '--model' => User::class,
            '--global' => true,
        ])->expectsOutput($expected);
    }

    public function test_command_generates_json_when_option_is_enabled()
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $this->markTestSkipped('Fails on windows because of /r/n characters');
        }

        $expected = $this->getExpectedContent('user.json', true);

        $this->artisan(ModelTyperCommand::class, [
            'output-file' => './test/output/user.json',
            '--model' => User::class,
            '--json' => true,
            '--use-enums' => true,
        ])->expectsOutput('Typescript interfaces generated in ./test/output/user.json file');

        $actual = $this->getGeneratedFileContents('user.json');

        $this->assertSame($expected, $actual);
    }

    public function test_command_generates_use_enums_when_option_is_enabled()
    {
        $expected = $this->getExpectedContent('user-enums.ts');

        $this->artisan(ModelTyperCommand::class, [
            '--model' => User::class,
            '--use-enums' => true,
        ])->expectsOutput($expected);
    }

    public function test_command_generates_plurals_when_option_is_enabled()
    {
        $expected = $this->getExpectedContent('user-plurals.ts');

        $this->artisan(ModelTyperCommand::class, [
            '--model' => User::class,
            '--plurals' => true,
        ])->expectsOutput($expected);
    }

    public function test_command_generates_no_relations_when_option_is_enabled()
    {
        $expected = $this->getExpectedContent('user-no-relations.ts');

        $this->artisan(ModelTyperCommand::class, [
            '--model' => User::class,
            '--no-relations' => true,
        ])->expectsOutput($expected);
    }

    public function test_command_generates_optional_relations_when_option_is_enabled()
    {
        $expected = $this->getExpectedContent('user-optional-relations.ts');

        $this->artisan(ModelTyperCommand::class, [
            '--model' => User::class,
            '--optional-relations' => true,
        ])->expectsOutput($expected);
    }

    public function test_command_generates_no_hidden_when_option_is_enabled()
    {
        $expected = $this->getExpectedContent('user-no-hidden.ts');

        $this->artisan(ModelTyperCommand::class, [
            '--model' => User::class,
            '--no-hidden' => true,
        ])->expectsOutput($expected);
    }

    public function test_command_generates_timestamps_date_when_option_is_enabled()
    {
        $expected = $this->getExpectedContent('user-timestamps-date.ts');

        $this->artisan(ModelTyperCommand::class, [
            '--model' => User::class,
            '--timestamps-date' => true,
        ])->expectsOutput($expected);
    }

    public function test_command_generates_optional_nullables_when_option_is_enabled()
    {
        $expected = $this->getExpectedContent('user-optional-nullables.ts');

        $this->artisan(ModelTyperCommand::class, [
            '--model' => User::class,
            '--optional-nullables' => true,
        ])->expectsOutput($expected);
    }

    public function test_command_generates_api_resources_when_option_is_enabled()
    {
        $expected = $this->getExpectedContent('user-api-resource.ts');

        $this->artisan(ModelTyperCommand::class, [
            '--model' => User::class,
            '--api-resources' => true,
        ])->expectsOutput($expected);
    }

    public function test_command_can_ignore_config_when_option_is_enabled()
    {
        Config::set('modeltyper.global', true);
        Config::set('modeltyper.fillables', false);
        Config::set('modeltyper.fillable-suffix', 'FillableSuffix');

        $expected = $this->getExpectedContent('user-fillables.ts');
        $this->artisan(ModelTyperCommand::class, [
            '--model' => User::class,
            '--fillables' => true,
            '--fillable-suffix' => 'Editable',
            '--ignore-config' => true,
        ])->expectsOutput($expected);
    }

    public function test_command_generates_expected_output_for_complex_model()
    {
        $expected = $this->getExpectedContent('complex-model.ts');
        $this->artisan(ModelTyperCommand::class, [
            '--model' => Complex::class,
        ])->expectsOutput($expected);
    }

    public function test_command_generates_expected_output_for_complex_model_when_user_types_unknown_custom_cast()
    {
        // set UpperCast return type in config
        Config::set('modeltyper.custom_mappings', [
            'App\Casts\UpperCast' => 'string',
        ]);

        $expected = $this->getExpectedContent('complex-model-with-cast.ts');
        $this->artisan(ModelTyperCommand::class, [
            '--model' => Complex::class,
        ])->expectsOutput($expected);
    }

    public function test_command_generates_expected_output_for_camel_case()
    {
        Config::set('modeltyper.case', [
            'columns' => 'camel',
            'relations' => 'camel',
        ]);

        $this->assertSame('camel', Config::get('modeltyper.case.columns'));

        $expected = $this->getExpectedContent('complex-model-camel-case.ts');
        $this->artisan(ModelTyperCommand::class, [
            '--model' => Complex::class,
        ])->expectsOutput($expected);
    }

    public function test_command_generates_expected_output_for_pascal_case()
    {
        Config::set('modeltyper.case', [
            'columns' => 'pascal',
            'relations' => 'pascal',
        ]);

        $this->assertSame('pascal', Config::get('modeltyper.case.columns'));

        $expected = $this->getExpectedContent('complex-model-pascal-case.ts');
        $this->artisan(ModelTyperCommand::class, [
            '--model' => Complex::class,
        ])->expectsOutput($expected);
    }
}
