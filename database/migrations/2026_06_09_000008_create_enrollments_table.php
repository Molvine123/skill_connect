<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('program_id')->constrained('skill_programs')->onDelete('cascade');
            $table->string('status')->default('pending'); // pending, approved, completed, cancelled, rejected
            $table->timestamps();
            $table->unique(['student_id', 'program_id']); // Student cannot enroll twice in the same program
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
