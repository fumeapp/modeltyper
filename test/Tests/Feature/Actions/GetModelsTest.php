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

    /** @test */
    public function testActionCanFindAllModelsInProject()
    {
        $action = app(GetModels::class);

        $foundModels = $action();

        $this->assertCount(3, $foundModels);
        $this->assertStringContainsString('Pivot.php', $foundModels[0]->getRelativePathname());
        $this->assertStringContainsString('User.php', $foundModels[1]->getRelativePathname());
        $this->assertStringContainsString('Team.php', $foundModels[2]->getRelativePathname());
    }
}
