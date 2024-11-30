<?php

namespace FumeApp\ModelTyper\Commands;

use FumeApp\ModelTyper\Actions\Generator;
use FumeApp\ModelTyper\Exceptions\ModelTyperException;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
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
                            {--fillables : Output model fillables}
                            {--fillable-suffix=}
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
                $this->option('global') ?: Config::get('modeltyper.global', false),
                $this->option('json') ?: Config::get('modeltyper.json', false),
                $this->option('use-enums') ?: Config::get('modeltyper.use-enums', false),
                ($this->option('plurals') || $this->option('all')) ?: Config::get('modeltyper.plurals', false),
                ($this->option('api-resources') || $this->option('all')) ?: Config::get('modeltyper.api-resources', false),
                $this->option('optional-relations') ?: Config::get('modeltyper.optional-relations', false),
                $this->option('no-relations') ?: Config::get('modeltyper.no-relations', false),
                $this->option('no-hidden') ?: Config::get('modeltyper.no-hidden', false),
                $this->option('timestamps-date') ?: Config::get('modeltyper.timestamps-date', false),
                $this->option('optional-nullables') ?: Config::get('modeltyper.optional-nullables', false),
                $this->option('fillables') ?: Config::get('modeltyper.fillables', false),
                $this->option('fillable-suffix') ?: Config::get('modeltyper.fillable-suffix', 'fillable'),
            );

            /** @var string|null $path */
            $path = $this->argument('output-file');

            if (is_null($path) && Config::get('modeltyper.output-file', false)) {
                $path = (string) Config::get('modeltyper.output-file-path', '');
            }

            if (! is_null($path) && strlen($path) > 0) {
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
