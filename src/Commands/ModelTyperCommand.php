<?php

namespace FumeApp\ModelTyper\Commands;

use FumeApp\ModelTyper\Actions\Generator;
use FumeApp\ModelTyper\Exceptions\ModelTyperException;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use ReflectionException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command as CommandAlias;

#[AsCommand(name: 'model:typer')]
class ModelTyperCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'model:typer';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'model:typer
                            {output-file? : Echo the definitions into a file}
                            {--model= : Generate typescript interfaces for a specific model}
                            {--global : Generate typescript interfaces in a global namespace named models}
                            {--json : Output the result as json}
                            {--use-enums : Use typescript enums instead of object literals}
                            {--plurals : Output model plurals}
                            {--no-relations : Do not include relations}
                            {--optional-relations : Make relations optional fields on the model type}
                            {--no-hidden : Do not include hidden model attributes}
                            {--no-counts : Do not include counts on relations}
                            {--optional-counts : Make counts on relations optional fields}
                            {--no-exists : Do not include exists on relations}
                            {--optional-exists : Make exists on relations optional fields}
                            {--no-sums : Do not include sums on relations}
                            {--optional-sums : Make sums on relations optional fields}
                            {--no-averages : Do not include averages on relations}
                            {--optional-averages : Make averages on relations optional fields}
                            {--timestamps-date : Output timestamps as a Date object type}
                            {--optional-nullables : Output nullable attributes as optional fields}
                            {--api-resources : Output api.MetApi interfaces}
                            {--fillables : Output model fillables}
                            {--fillable-suffix= : Appends to fillables}
                            {--ignore-config : Ignore options set in config}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate typescript interfaces for all found models';

    /**
     * Create a new command instance.
     */
    public function __construct(protected Filesystem $files)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @throws ReflectionException
     */
    public function handle(Generator $generator): int
    {
        try {
            $output = $generator(
                specificModel: $this->option('model'),
                global: $this->getConfig('global'),
                json: $this->getConfig('json'),
                useEnums: $this->getConfig('use-enums'),
                plurals: $this->getConfig('plurals'),
                apiResources: $this->getConfig('api-resources'),
                optionalRelations: $this->getConfig('optional-relations'),
                noRelations: $this->getConfig('no-relations'),
                noHidden: $this->getConfig('no-hidden'),
                noCounts: $this->getConfig('no-counts'),
                optionalCounts: $this->getConfig('optional-counts'),
                noExists: $this->getConfig('no-exists'),
                optionalExists: $this->getConfig('optional-exists'),
                noSums: $this->getConfig('no-sums'),
                optionalSums: $this->getConfig('optional-sums'),
                noAverages: $this->getConfig('no-averages'),
                optionalAverages: $this->getConfig('optional-averages'),
                timestampsDate: $this->getConfig('timestamps-date'),
                optionalNullables: $this->getConfig('optional-nullables'),
                fillables: $this->getConfig('fillables'),
                fillableSuffix: $this->getConfig('fillable-suffix'),
            );

            /** @var string|null $path */
            $path = $this->argument('output-file');

            if (is_null($path) && Config::get('modeltyper.output-file', false)) {
                $path = (string) Config::get('modeltyper.output-file-path', '');
            }

            if (! is_null($path) && mb_strlen($path) > 0) {
                $this->files->ensureDirectoryExists(dirname($path));
                $this->files->put($path, $output);

                $this->info('Typescript interfaces generated in ' . $path . ' file');

                return CommandAlias::SUCCESS;
            }

            $this->line($output);
        } catch (ModelTyperException $exception) {
            $this->error($exception->getMessage());

            return CommandAlias::FAILURE;
        }

        return CommandAlias::SUCCESS;
    }

    private function getConfig(string $key): string|bool
    {
        if ($this->option('ignore-config')) {
            return $this->option($key);
        }

        return $this->option($key) ?: Config::get("modeltyper.{$key}");
    }
}
