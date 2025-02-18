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
        Schema::create('datafeeds', function (Blueprint $table) {
            $table->id();
            $table->string('data_type');
            $table->string('label');
            $table->integer('data');
            $table->integer('dataset_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('datafeeds');
    }
};
