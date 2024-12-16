<?php

namespace Tests\Feature\Console;

use FumeApp\ModelTyper\Commands\ShowModelTyperMappingsCommand;
use FumeApp\ModelTyper\Constants\TypescriptMappings;
use Tests\TestCase;

class ShowModelTyperMappingsCommandTest extends TestCase
{
    public function test_command_can_be_executed_successfully()
    {
        $this->artisan(ShowModelTyperMappingsCommand::class)->assertSuccessful();
    }

    public function test_command_generates_expected_output()
    {
        TypescriptMappings::$mappings = [
            'a' => '1',
            'b' => '2',
        ];

        $this->artisan(ShowModelTyperMappingsCommand::class)
            ->expectsTable(
                headers: ['From PHP Type', 'To TypeScript Type'],
                rows: [
                    ['a', '1'],
                    ['b', '2'],
                ]
            )
            ->expectsOutputToContain('Showing type conversion table using timestamps-date set to false');
    }
}
