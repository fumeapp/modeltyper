<?php

namespace FumeApp\ModelTyper\Tests;

use FumeApp\ModelTyper\ModelTyperServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            ModelTyperServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        // TODO: Figure out how to load from a .env.testing file
    }

    public function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('users');
        Schema::dropIfExists('posts');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('name');
        });
    }
}
