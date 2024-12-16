<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('complex_model_table', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('bigInteger');
            $table->binary('binary');
            $table->boolean('boolean');
            $table->char('char');
            $table->dateTime('dateTime');
            $table->dateTime('immutableDateTime');
            $table->dateTime('immutableCustomDateTime');
            $table->date('date');
            $table->date('immutable_date');
            $table->decimal('decimal');
            $table->double('double');
            $table->enum('enum', [1, 2, 3, 'A', 'B']);
            $table->float('float');
            $table->integer('integer');
            $table->ipAddress('ipAddress');
            $table->json('json');
            $table->jsonb('jsonb');
            $table->longText('longText');
            $table->macAddress('macAddress');
            $table->mediumInteger('mediumInteger');
            $table->mediumText('mediumText');
            $table->smallInteger('smallInteger');
            $table->string('string');
            $table->string('casted_uppercase_string');
            $table->text('text');
            $table->time('time');
            $table->timestamp('timestamp');
            $table->year('year');
            $table->uuid('uuid');
            $table->ulid('ulid');
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
