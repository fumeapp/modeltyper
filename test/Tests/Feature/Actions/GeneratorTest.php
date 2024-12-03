<?php

namespace Tests\Feature\Actions;

use App\Models\AbstractModel;
use FumeApp\ModelTyper\Actions\Generator;
use FumeApp\ModelTyper\Exceptions\ModelTyperException;
use Tests\TestCase;

class GeneratorTest extends TestCase
{
    public function test_action_can_be_resolved_by_application()
    {
        $this->assertInstanceOf(Generator::class, resolve(Generator::class));
    }

    public function test_action_can_be_executed()
    {
        $action = app(Generator::class);
        $result = $action();

        $this->assertIsString($result);
    }

    public function test_action_throws_exception_on_non_existent_class()
    {
        $this->expectException(ModelTyperException::class);
        $this->expectExceptionMessage('No models found.');

        $action = app(Generator::class);
        $action('nonExistentClass');
    }

    public function test_action_throws_exception_on_abstract_model()
    {
        $this->expectException(ModelTyperException::class);
        $this->expectExceptionMessage('No models found.');

        $action = app(Generator::class);
        $action(AbstractModel::class);
    }
}
