<?php

namespace FumeApp\ModelTyper\Commands;

use FumeApp\ModelTyper\Actions\Generator;
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
                            {--json : Output the result as json}';

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
     *
     * @param  Generator  $generator
     * @return int
     */
    public function handle(Generator $generator): int
    {
        // determine Laravel version
        $laravelVersion = (float) app()->version();

        if ($laravelVersion < 9.20) {
            $this->error('This package requires Laravel 9.2 or higher.');

            return Command::FAILURE;
        }

        // check if doctrine/dbal is installed
        if (! class_exists('Doctrine\DBAL\DriverManager')) {
            $this->error('This package requires doctrine/dbal to be installed.');

            return Command::FAILURE;
        }

        echo $generator($this->option('model'), $this->option('global'), $this->option('json'));

        return Command::SUCCESS;
    }
}
