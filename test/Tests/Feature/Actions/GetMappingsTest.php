<?php

namespace Tests\Feature\Actions;

use FumeApp\ModelTyper\Actions\GetMappings;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class GetMappingsTest extends TestCase
{
    public function test_action_can_be_resolved_by_application()
    {
        $this->assertInstanceOf(GetMappings::class, resolve(GetMappings::class));
    }

    public function test_action_can_set_timestamps_as_date()
    {
        $action = app(GetMappings::class);

        $mappings = $action(setTimestampsToDate: true);

        $this->assertArrayHasKey('date', $mappings);
        $this->assertArrayHasKey('immutable_date', $mappings);
        $this->assertArrayHasKey('datetime', $mappings);
        $this->assertArrayHasKey('immutable_datetime', $mappings);
        $this->assertArrayHasKey('immutable_custom_datetime', $mappings);
        $this->assertArrayHasKey('timestamp', $mappings);

        $this->assertEquals('Date', $mappings['date']);
        $this->assertEquals('Date', $mappings['immutable_date']);
        $this->assertEquals('Date', $mappings['datetime']);
        $this->assertEquals('Date', $mappings['immutable_datetime']);
        $this->assertEquals('Date', $mappings['immutable_custom_datetime']);
        $this->assertEquals('Date', $mappings['timestamp']);
    }

    public function test_action_can_merge_user_config()
    {
        Config::set('modeltyper.custom_mappings', [
            'userDefinedConfig' => 'SomeType',
        ]);

        $action = app(GetMappings::class);

        $mappings = $action();

        $this->assertArrayHasKey('userdefinedconfig', $mappings);
        $this->assertEquals('SomeType', $mappings['userdefinedconfig']);
    }

    public function test_action_can_use_user_config_to_override_default_mappings()
    {
        $action = app(GetMappings::class);

        $mappings = $action();

        $this->assertArrayHasKey('text', $mappings);
        $this->assertEquals('string', $mappings['text']);

        Config::set('modeltyper.custom_mappings', [
            'text' => 'number',
        ]);

        $mappings = $action();

        $this->assertEquals('number', $mappings['text']);
    }
}
