<?php

namespace Tests\Feature\Actions;

use App\Models\User;
use FumeApp\ModelTyper\Actions\BuildModelDetails;
use FumeApp\ModelTyper\Actions\GetModels;
use Tests\TestCase;

class BuildModelDetailsTest extends TestCase
{
    public function test_action_can_be_resolved_by_application()
    {
        $this->assertInstanceOf(BuildModelDetails::class, resolve(BuildModelDetails::class));
    }

    public function test_action_can_be_executed()
    {
        $models = app(GetModels::class)(User::class);
        $action = app(BuildModelDetails::class);

        $result = $action($models->first());

        $this->assertIsArray($result);

        $this->assertArrayHasKey('reflectionModel', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertArrayHasKey('columns', $result);
        $this->assertArrayHasKey('nonColumns', $result);
        $this->assertArrayHasKey('relations', $result);
        $this->assertArrayHasKey('interfaces', $result);
        $this->assertArrayHasKey('imports', $result);
    }
}
