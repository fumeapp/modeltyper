<?php

namespace Tests\Feature\Actions;

use FumeApp\ModelTyper\Actions\WriteSum;
use Tests\TestCase;

class WriteSumTest extends TestCase
{
    protected array $sum = [
        'relation' => 'orders',
        'column' => 'total',
    ];

    public function test_action_can_be_resolved_by_application()
    {
        $this->assertInstanceOf(WriteSum::class, resolve(WriteSum::class));
    }

    public function test_action_can_be_executed()
    {
        $action = app(WriteSum::class);
        $result = $action($this->sum);

        $this->assertIsString($result);
        $this->assertStringContainsString('orders_sum_total: number | null', $result);
    }

    public function test_action_can_return_array()
    {
        $action = app(WriteSum::class);
        $result = $action($this->sum, jsonOutput: true);

        $this->assertIsArray($result);
        $this->assertEquals([
            'name' => 'orders_sum_total',
            'type' => 'number | null',
        ], $result);
    }

    public function test_action_can_be_indented()
    {
        $action = app(WriteSum::class);
        $result = $action($this->sum, indent: 'ASDF');

        $this->assertStringContainsString('ASDF  orders_sum_total: number | null', $result);
    }

    public function test_action_can_return_optional_sums()
    {
        $action = app(WriteSum::class);
        $result = $action($this->sum, optionalSums: true);

        $this->assertStringContainsString('orders_sum_total?: number | null', $result);
    }

    public function test_action_can_return_optional_sums_with_pascal_case()
    {
        config(['modeltyper.case.columns' => 'pascal']);
        $action = app(WriteSum::class);
        $result = $action($this->sum, optionalSums: true);

        $this->assertStringContainsString('OrdersSumTotal?: number | null', $result);
    }

    public function test_action_can_return_optional_sums_as_array()
    {
        $action = app(WriteSum::class);
        $result = $action($this->sum, jsonOutput: true, optionalSums: true);

        $this->assertIsArray($result);
        $this->assertEquals([
            'name' => 'orders_sum_total?',
            'type' => 'number | null',
        ], $result);
    }

    public function test_action_can_return_optional_sums_with_camel_case()
    {
        config(['modeltyper.case.columns' => 'camel']);
        $action = app(WriteSum::class);
        $result = $action($this->sum, optionalSums: true);

        $this->assertStringContainsString('ordersSumTotal?: number | null', $result);
    }
}
