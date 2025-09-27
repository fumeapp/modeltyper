<?php

namespace FumeApp\ModelTyper\Commands;

use FumeApp\ModelTyper\Actions\GetMappings;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'model:typer-mappings')]
class ShowModelTyperMappingsCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'model:typer-mappings';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'model:typer-mappings
                            {--timestamps-date : Output timestamps as a Date object type}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show mappings used for generating TypeScript definitions';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $timestampsAsDate = (bool) $this->option('timestamps-date') ?: Config::get('modeltyper.timestamps-date', false);

            $mappings = collect(app(GetMappings::class)(setTimestampsToDate: $timestampsAsDate))
                ->map(fn (string $mappings, string $key): array => [$key, $mappings])
                ->values()
                ->toArray();

            $this->table(headers: ['From PHP Type', 'To TypeScript Type'], rows: $mappings);

            $this->info('Showing type conversion table using timestamps-date set to ' . ($timestampsAsDate ? 'true' : 'false'));
        } catch (\Throwable $throwable) {
            $this->error($throwable->getMessage());

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
