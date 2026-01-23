<?php

namespace Tests\Feature\Overrides;

use FumeApp\ModelTyper\Overrides\ModelInspector;
use Illuminate\Support\Facades\Config;
use ReflectionClass;
use Tests\TestCase;

class ModelInspectorTest extends TestCase
{
    public function test_override_can_be_resolved_by_application()
    {
        $this->assertInstanceOf(ModelInspector::class, resolve(ModelInspector::class));
    }

    public function test_override_can_set_custom_relationships()
    {
        // set custom_relationships in config
        Config::set('modeltyper.custom_relationships', [
            'hasCustomRelationship',
            'belongsToCustomRelationship',
        ]);

        $override = app(ModelInspector::class);

        $relationMethods = (new ReflectionClass($override))
            ->getProperty('relationMethods')
            ->getValue($override);

        $this->assertIsArray($relationMethods);
        $this->assertContains(needle: 'hasCustomRelationship', haystack: $relationMethods);
        $this->assertContains(needle: 'belongsToCustomRelationship', haystack: $relationMethods);
    }

    public function test_override_can_detect_nullable_return_types()
    {
        $override = app(ModelInspector::class);

        // Test the isReturnTypeNullable method with a mock reflection method
        $reflectionMethod = $this->createMock(\ReflectionMethod::class);

        // Test with nullable union type (e.g., BelongsTo|Listing|null)
        $nullableUnionType = $this->createMock(\ReflectionUnionType::class);
        $nullType = $this->createMock(\ReflectionNamedType::class);
        $nullType->method('getName')->willReturn('null');

        $nullableUnionType->method('getTypes')->willReturn([$nullType]);

        $reflectionMethod->method('getReturnType')->willReturn($nullableUnionType);

        $result = $override->isReturnTypeNullable($reflectionMethod);
        $this->assertTrue($result);
    }

    public function test_override_can_detect_non_nullable_return_types()
    {
        $override = app(ModelInspector::class);

        // Test with non-nullable type
        $reflectionMethod = $this->createMock(\ReflectionMethod::class);
        $nonNullableType = $this->createMock(\ReflectionNamedType::class);
        $nonNullableType->method('getName')->willReturn('BelongsTo');
        $nonNullableType->method('allowsNull')->willReturn(false);

        $reflectionMethod->method('getReturnType')->willReturn($nonNullableType);

        $result = $override->isReturnTypeNullable($reflectionMethod);
        $this->assertFalse($result);
    }

    public function test_override_can_detect_nullable_named_type()
    {
        $override = app(ModelInspector::class);

        // Test with nullable named type (e.g., ?BelongsTo)
        $reflectionMethod = $this->createMock(\ReflectionMethod::class);
        $nullableType = $this->createMock(\ReflectionNamedType::class);
        $nullableType->method('getName')->willReturn('BelongsTo');
        $nullableType->method('allowsNull')->willReturn(true);

        $reflectionMethod->method('getReturnType')->willReturn($nullableType);

        $result = $override->isReturnTypeNullable($reflectionMethod);
        $this->assertTrue($result);
    }
}
