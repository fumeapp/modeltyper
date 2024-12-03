<?php

namespace Tests\Feature\Actions;

use App\Models\User;
use Exception;
use FumeApp\ModelTyper\Actions\DetermineAccessorType;
use ReflectionClass;
use ReflectionMethod;
use Tests\TestCase;
use Tests\Traits\ResolveClassAsReflection;

class DetermineAccessorTypeTest extends TestCase
{
    use ResolveClassAsReflection;

    public function test_action_can_be_resolved_by_application()
    {
        $this->assertInstanceOf(DetermineAccessorType::class, resolve(DetermineAccessorType::class));
    }

    public function test_action_can_be_executed()
    {
        $action = app(DetermineAccessorType::class);
        $result = $action(new ReflectionClass(User::class), 'roleNew');

        $this->assertTrue($result instanceof ReflectionMethod);
    }

    public function test_action_can_be_executed_for_traditional_accessor()
    {
        $action = app(DetermineAccessorType::class);
        $result = $action(new ReflectionClass(User::class), 'getRoleTraditionalAttribute');

        $this->assertTrue($result instanceof ReflectionMethod);
    }

    public function test_action_throws_exception_on_non_existent_accessor()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Accessor method for NonExistentAccessor on model ' . User::class . ' does not exist');

        $action = app(DetermineAccessorType::class);
        $action(new ReflectionClass(User::class), 'nonExistentAccessor');
    }
}
