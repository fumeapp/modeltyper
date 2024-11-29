<?php

namespace FumeApp\ModelTyper\Commands;

use FumeApp\ModelTyper\Actions\Generator;
use FumeApp\ModelTyper\Exceptions\ModelTyperException;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Attribute\AsCommand;

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
                            {--timestamps-date : Output timestamps as a Date object type}
                            {--optional-nullables : Output nullable attributes as optional fields}
                            {--api-resources : Output api.MetApi interfaces}
                            {--resolve-abstract : Attempt to resolve abstract models)}
                            {--fillables : Output model fillables}
                            {--fillable-suffix=fillable}
                            {--all : Enable all output options (equivalent to --plurals --api-resources)}';

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
     */
    public function handle(Generator $generator): int
    {
        try {
            $output = $generator(
                $this->option('model'),
                $this->option('global'),
                $this->option('json'),
                $this->option('use-enums'),
                $this->option('plurals') || $this->option('all'),
                $this->option('api-resources') || $this->option('all'),
                $this->option('optional-relations'),
                $this->option('no-relations'),
                $this->option('no-hidden'),
                $this->option('timestamps-date'),
                $this->option('optional-nullables'),
                $this->option('resolve-abstract'),
                $this->option('fillables'),
                $this->option('fillable-suffix')
            );

            /** @var string|null $path */
            $path = $this->argument('output-file');

            if (! is_null($path)) {
                $this->files->ensureDirectoryExists(dirname($path));
                $this->files->put($path, $output);

                $this->info('Typescript interfaces generated in ' . $path . ' file');

                return Command::SUCCESS;
            }

            $this->line($output);
        } catch (ModelTyperException $exception) {
            $this->error($exception->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
