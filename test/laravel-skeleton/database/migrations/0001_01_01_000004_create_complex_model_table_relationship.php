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
        Schema::create('complex_model_table_relationship', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complex_model_table_id')->constrained('complex_model_table');
        });
    }
};
