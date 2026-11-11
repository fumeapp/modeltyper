<?php

namespace Tests\Feature\Actions;

use FumeApp\ModelTyper\Actions\WriteFillable;
use Illuminate\Database\Eloquent\Model;
use ReflectionClass;
use Tests\TestCase;

class TestModel extends Model
{
    protected $hidden = [
        'password',
    ];

    protected $fillable = [
        'name',
        'password',
    ];
}

class WriteFillableTest extends TestCase
{
    public function test_action_can_be_resolved_by_application()
    {
        $this->assertInstanceOf(WriteFillable::class, resolve(WriteFillable::class));
    }

    public function test_action_can_be_executed()
    {
        $action = app(WriteFillable::class);
        $result = $action(new ReflectionClass(TestModel::class), 'TestModel', 'Fillable', '  ', false);

        $this->assertIsString($result);
    }

    public function test_hides_hidden_fillable_attributes_when_no_hidden_is_true()
    {
        $action = app(WriteFillable::class);
        $result = $action(new ReflectionClass(TestModel::class), 'TestModel', 'Fillable', '  ', true);

        $this->assertIsString($result);
        $this->assertStringContainsString('name', $result);
        $this->assertStringNotContainsString('password', $result);
    }

    public function test_keeps_hidden_fillable_attributes_when_no_hidden_is_false()
    {
        $action = app(WriteFillable::class);
        $result = $action(new ReflectionClass(TestModel::class), 'TestModel', 'Fillable', '  ', false);

        $this->assertIsString($result);
        $this->assertStringContainsString('name', $result);
        $this->assertStringContainsString('password', $result);
    }
}
