<?php

namespace Tests\Feature\Actions;

use FumeApp\ModelTyper\Actions\WriteAvg;
use Tests\TestCase;

class WriteAvgTest extends TestCase
{
    protected array $avg = [
        'relation' => 'orders',
        'column' => 'total',
    ];

    public function test_action_can_be_resolved_by_application()
    {
        $this->assertInstanceOf(WriteAvg::class, resolve(WriteAvg::class));
    }

    public function test_action_can_be_executed()
    {
        $action = app(WriteAvg::class);
        $result = $action($this->avg);

        $this->assertIsString($result);
        $this->assertStringContainsString('orders_avg_total: number | null', $result);
    }

    public function test_action_can_return_array()
    {
        $action = app(WriteAvg::class);
        $result = $action($this->avg, jsonOutput: true);

        $this->assertIsArray($result);
        $this->assertEquals([
            'name' => 'orders_avg_total',
            'type' => 'number | null',
        ], $result);
    }

    public function test_action_can_be_indented()
    {
        $action = app(WriteAvg::class);
        $result = $action($this->avg, indent: 'ASDF');

        $this->assertStringContainsString('ASDF  orders_avg_total: number | null', $result);
    }

    public function test_action_can_return_optional_averages()
    {
        $action = app(WriteAvg::class);
        $result = $action($this->avg, optionalAverages: true);

        $this->assertStringContainsString('orders_avg_total?: number | null', $result);
    }

    public function test_action_can_return_optional_averages_with_pascal_case()
    {
        config(['modeltyper.case.columns' => 'pascal']);
        $action = app(WriteAvg::class);
        $result = $action($this->avg, optionalAverages: true);

        $this->assertStringContainsString('OrdersAvgTotal?: number | null', $result);
    }

    public function test_action_can_return_optional_averages_as_array()
    {
        $action = app(WriteAvg::class);
        $result = $action($this->avg, jsonOutput: true, optionalAverages: true);

        $this->assertIsArray($result);
        $this->assertEquals([
            'name' => 'orders_avg_total?',
            'type' => 'number | null',
        ], $result);
    }

    public function test_action_can_return_optional_averages_with_camel_case()
    {
        config(['modeltyper.case.columns' => 'camel']);
        $action = app(WriteAvg::class);
        $result = $action($this->avg, optionalAverages: true);

        $this->assertStringContainsString('ordersAvgTotal?: number | null', $result);
    }
}
