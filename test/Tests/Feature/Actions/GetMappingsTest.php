<?php

namespace Tests\Feature\Actions;

use FumeApp\ModelTyper\Actions\GetMappings;
use Illuminate\Support\Facades\Config;
use Tests\Feature\TestCase;

class GetMappingsTest extends TestCase
{
    /** @test */
    public function testActionCanBeResolvedByApplication()
    {
        $this->assertInstanceOf(GetMappings::class, resolve(GetMappings::class));
    }

    /** @test */
    public function testActionCanSetTimestampsAsDate()
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

    /** @test */
    public function testActionCanMergeUserConfig()
    {
        Config::set('modeltyper.custom_mappings', [
            'userDefinedConfig' => 'SomeType',
        ]);

        $action = app(GetMappings::class);

        $mappings = $action();

        $this->assertArrayHasKey('userdefinedconfig', $mappings);
        $this->assertEquals('SomeType', $mappings['userdefinedconfig']);
    }

    /** @test */
    public function testActionCanUseUserConfigToOverrideDefaultMappings()
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
