<?php

namespace FumeApp\ModelTyper\Commands;

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
    protected $signature = 'model:typer {--global}';

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
     * @throws ReflectionException
     */
    public function handle()
    {
        echo  (new ModelInterface($this->option('global')))->generate();
        return 0;
    }
}
