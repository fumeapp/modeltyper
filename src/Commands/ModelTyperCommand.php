<?php

namespace FumeApp\ModelTyper\Commands;

use FumeApp\ModelTyper\Actions\Generator;
use FumeApp\ModelTyper\Exceptions\ModelTyperException;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

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
                            {--fillable-suffix= : Appends to fillables}
                            {--ignore-config : Ignore options set in config}
                            {--no-counts : Disable countable relations}
                            {--optional-counts : Make countable relations optional}';

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

    protected function getOptions(): array
    {
        return [
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'Generate typescript interface for a specific model'],
            ['output-file', 'o', InputOption::VALUE_OPTIONAL, 'Output file path'],
            ['global', 'g', InputOption::VALUE_NONE, 'Output global namespace'],
            ['json', 'j', InputOption::VALUE_NONE, 'Output json format'],
            ['use-enums', 'e', InputOption::VALUE_NONE, 'Use typescript enums'],
            ['plurals', 'p', InputOption::VALUE_NONE, 'Output plural form for models'],
            ['api-resources', 'a', InputOption::VALUE_NONE, 'Output api.MetApi interfaces'],
            ['optional-relations', 'r', InputOption::VALUE_NONE, 'Make model relationships optional'],
            ['no-relations', 'R', InputOption::VALUE_NONE, 'Exclude model relationships'],
            ['no-counts', 'c', InputOption::VALUE_NONE, 'Disable countable relations'],
            ['optional-counts', 'C', InputOption::VALUE_NONE, 'Make countable relations optional'],
            ['no-hidden', 'H', InputOption::VALUE_NONE, 'Exclude hidden model attributes'],
            ['timestamps-date', 't', InputOption::VALUE_NONE, 'Output timestamps as date object types'],
            ['optional-nullables', 'n', InputOption::VALUE_NONE, 'Make nullable attributes optional'],
            ['fillables', 'f', InputOption::VALUE_NONE, 'Output fillable model attributes'],
            ['fillable-suffix', 's', InputOption::VALUE_OPTIONAL, 'Suffix for fillable model attributes', 'fillable'],
        ];
    }

    /**
     * Execute the console command.
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
                noCounts: $this->getConfig('no-counts'),
                optionalCounts: $this->getConfig('optional-counts'),
                noHidden: $this->getConfig('no-hidden'),
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

    private function getConfig(string $key): string|bool
    {
        if ($this->option('ignore-config')) {
            return $this->option($key);
        }

        return $this->option($key) ?: Config::get("modeltyper.{$key}");
    }
}
