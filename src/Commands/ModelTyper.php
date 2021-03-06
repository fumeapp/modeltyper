<?php

namespace FumeApp\ModelTyper\Commands;

use Doctrine\DBAL\Exception;
use FumeApp\ModelTyper\ModelInterface;
use Illuminate\Console\Command;
use ReflectionException;

class ModelTyper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'model:typer
                            {--global : Generate your interfaces in a global namespace named model}';

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
     * @return int
     * @throws ReflectionException|Exception
     */
    public function handle(): int
    {
        echo  (new ModelInterface($this->option('global')))->generate();

        return Command::SUCCESS;
    }
}
