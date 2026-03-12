<?php

namespace Tests\Feature\Actions;

use App\Models\User;
use App\Modules\Models\Team;
use FumeApp\ModelTyper\Actions\GetModels;
use FumeApp\ModelTyper\Exceptions\ModelTyperException;
use Tests\TestCase;

class GetModelsTest extends TestCase
{
    public function test_action_can_be_resolved_by_application()
    {
        $this->assertInstanceOf(GetModels::class, resolve(GetModels::class));
    }

    public function test_action_returns_only_one_file_when_model_is_specified()
    {
        $action = app(GetModels::class);

        $foundModels = $action(model: 'User')->map(fn ($file) => $file->getBasename());

        $this->assertCount(1, $foundModels);
        $this->assertStringContainsString('User.php', $foundModels[0]);
    }

    public function test_action_accepts_fully_qualified_classname_as_model()
    {
        $action = app(GetModels::class);

        $foundModels = $action(model: User::class)->map(fn ($file) => $file->getBasename());

        $this->assertCount(1, $foundModels);
        $this->assertStringContainsString('User.php', $foundModels[0]);
    }

    public function test_action_can_find_all_models_in_project()
    {
        $action = app(GetModels::class);

        $foundModels = $action()->map(fn ($file) => $file->getBasename());

        $this->assertCount(6, $foundModels);
        $this->assertStringContainsString('Complex.php', $foundModels[0]);
        $this->assertStringContainsString('ComplexRelationship.php', $foundModels[1]);
        $this->assertStringContainsString('MorphRelation.php', $foundModels[2]);
        $this->assertStringContainsString('Pivot.php', $foundModels[3]);
        $this->assertStringContainsString('Team.php', $foundModels[4]);
        $this->assertStringContainsString('User.php', $foundModels[5]);
    }

    public function test_action_can_find_all_models_in_project_except_excluded_models()
    {
        $action = app(GetModels::class);

        $foundModels = $action(excludedModels: [User::class])->map(fn ($file) => $file->getBasename());

        $this->assertCount(5, $foundModels);
        $this->assertStringContainsString('Complex.php', $foundModels[0]);
        $this->assertStringContainsString('ComplexRelationship.php', $foundModels[1]);
        $this->assertStringContainsString('MorphRelation.php', $foundModels[2]);
        $this->assertStringContainsString('Pivot.php', $foundModels[3]);
        $this->assertStringContainsString('Team.php', $foundModels[4]);
    }

    public function test_action_can_find_all_models_in_project_when_in_included_models()
    {
        $action = app(GetModels::class);

        $foundModels = $action(includedModels: [User::class, Team::class], excludedModels: [User::class])->map(fn ($file) => $file->getBasename());

        $this->assertCount(1, $foundModels);
        $this->assertStringContainsString('Team.php', $foundModels[0]);
    }

    public function test_action_can_find_additional_paths_model()
    {
        $action = app(GetModels::class);

        $foundModels = $action(additionalPaths: [base_path('other')])->map(fn ($file) => $file->getBasename());

        $this->assertCount(7, $foundModels);
        $this->assertContains('VendorComplex.php', $foundModels);
    }

    public function test_action_throw_when_passing_empty_model_name_in_included_models()
    {
        $this->expectException(ModelTyperException::class);
        $this->expectExceptionMessage('Empty model name.');

        $action = app(GetModels::class);
        $action(includedModels: ['']);
    }

    public function test_action_throw_when_passing_empty_model_name_in_excluded_models()
    {
        $this->expectException(ModelTyperException::class);
        $this->expectExceptionMessage('Empty model name.');

        $action = app(GetModels::class);
        $action(excludedModels: ['']);
    }
}
