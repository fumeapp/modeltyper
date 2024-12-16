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
}
