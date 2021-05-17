<?php

namespace App\Console\Commands;

use App\ModelTyper\ModelInterface;
use Illuminate\Console\Command;

class ModelTyper extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'model:type';

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
     */
    public function handle()
    {
        echo  (new ModelInterface())->generate();
        return 0;
    }
}
