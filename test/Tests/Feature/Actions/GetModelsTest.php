<?php

namespace Tests\Feature\Actions;

use App\Models\User;
use FumeApp\ModelTyper\Actions\GetModels;
use Tests\Feature\TestCase;

class GetModelsTest extends TestCase
{
    /** @test */
    public function test_action_can_be_resolved_by_application()
    {
        $this->assertInstanceOf(GetModels::class, resolve(GetModels::class));
    }

    /** @test */
    public function test_action_returns_only_one_file_when_model_is_specified()
    {
        $action = app(GetModels::class);
        $this->assertCount(1, $action('User'));
    }

    /** @test */
    public function test_action_accepts_fully_qualified_classname_as_model()
    {
        $action = app(GetModels::class);
        $this->assertCount(1, $action(User::class));
    }

    /** @test */
    public function test_action_can_find_all_models_in_project()
    {
        $action = app(GetModels::class);

        $foundModels = $action();

        $this->assertCount(4, $foundModels);
        $this->assertStringContainsString('Complex.php', $foundModels[0]->getRelativePathname());
        $this->assertStringContainsString('Pivot.php', $foundModels[1]->getRelativePathname());
        $this->assertStringContainsString('User.php', $foundModels[2]->getRelativePathname());
        $this->assertStringContainsString('Team.php', $foundModels[3]->getRelativePathname());
    }
}
