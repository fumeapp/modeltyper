<?php

namespace Tests\Feature\Actions;

use App\Models\User;
use FumeApp\ModelTyper\Actions\GetModels;
use Tests\Feature\TestCase;

class GetModelsTest extends TestCase
{
    /** @test */
    public function testActionCanBeResolvedByApplication()
    {
        $this->assertInstanceOf(GetModels::class, resolve(GetModels::class));
    }

    /** @test */
    public function testActionReturnsOnlyOneFileWhenModelIsSpecified()
    {
        $action = app(GetModels::class);
        $this->assertCount(1, $action('User'));
    }

    /** @test */
    public function testActionAcceptsFullyQualifiedClassnameAsModel()
    {
        $action = app(GetModels::class);
        $this->assertCount(1, $action(User::class));
    }
}
