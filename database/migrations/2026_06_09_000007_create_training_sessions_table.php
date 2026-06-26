<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('skill_programs')->onDelete('cascade');
            $table->string('title'); // e.g. "Session 1: Getting Started"
            $table->text('description')->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('venue')->nullable();
            $table->string('meeting_link')->nullable(); // For online/hybrid sessions
            $table->integer('max_participants')->nullable();
            $table->text('trainer_information')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_sessions');
    }
};
