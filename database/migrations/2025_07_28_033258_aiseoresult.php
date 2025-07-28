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
        Schema::create('api_results', function (Blueprint $table) {
            // id(): Creates an auto-incrementing, unsigned BIGINT primary key 'id'.
            // This is the standard in Laravel.
            $table->id();

            // Store the prompt that was sent to the APIs.
            $table->text('prompt');

            // Use LONGTEXT to ensure you have enough space for long API responses.
            $table->longText('openai_response')->nullable();
            $table->longText('gemini_response')->nullable();

            // timestamps(): Creates 'created_at' and 'updated_at' columns.
            // 'created_at' will automatically store the date and time when a record is created.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_results');
    }
};