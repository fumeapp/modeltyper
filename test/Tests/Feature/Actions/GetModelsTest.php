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
        $action = new GetModels('User');
        $this->assertCount(1, $action());
    }

    public function testActionAcceptsFullyQualifiedClassnameAsModel()
    {
        $action = new GetModels(User::class);
        $this->assertCount(1, $action());
    }
}
