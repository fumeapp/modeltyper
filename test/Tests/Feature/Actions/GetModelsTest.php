<?php

namespace Tests\Feature\Actions;

use App\Models\User;
use FumeApp\ModelTyper\Actions\GetModels;
use Tests\Feature\TestCase;

class GetModelsTest extends TestCase
{
    public function testActionCanBeResolvedByApplication()
    {
        $this->assertInstanceOf(GetModels::class, resolve(GetModels::class));
    }

    public function testActionReturnsOnlyOneFileWhenModelIsSpecified()
    {
        $action = new GetModels;
        $this->assertCount(1, $action('User'));
    }

    public function testActionAcceptsFullyQualifiedClassnameAsModel()
    {
        $action = new GetModels;
        $this->assertCount(1, $action(User::class));
    }
}
