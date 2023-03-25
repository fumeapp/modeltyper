<?php

namespace FumeApp\ModelTyper\Commands;

use FumeApp\ModelTyper\Actions\Generator;
use FumeApp\ModelTyper\Exceptions\ModelTyperException;
use Illuminate\Console\Command;

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
                            {--model= : Generate your interfaces for a specific model}
                            {--global : Generate your interfaces in a global namespace named models}
                            {--json : Output the result as json}
                            {--plurals : Output model plurals}
                            {--no-relations : Do not include relations}
                            {--optional-relations : Make relations optional fields on the model type}
                            {--no-hidden : Do not include hidden model attributes}
                            {--timestamps-date : Output timestamps as a Date object type}
                            {--optional-nullables : Output nullable attributes as optional fields}
                            {--api-resources : Output api.MetApi interfaces}
                            {--all : Enable all output options (equivalent to --plurals --api-resources)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate interfaces for all found models';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(Generator $generator): int
    {
        // determine Laravel version
        $laravelVersion = (float) app()->version();

        if ($laravelVersion < 9.20) {
            $this->error('This package requires Laravel 9.20 or higher.');
            return Command::FAILURE;
        }

        try {
            echo $generator(
                $this->option('model'),
                $this->option('global'),
                $this->option('json'),
                $this->option('plurals') || $this->option('all'),
                $this->option('api-resources') || $this->option('all'),
                $this->option('optional-relations'),
                $this->option('no-relations'),
                $this->option('no-hidden'),
                $this->option('timestamps-date'),
                $this->option('optional-nullables')
            );
        } catch(ModelTyperException $exception) {
            $this->error($exception->getMessage());
        }

        return Command::SUCCESS;
    }
}
