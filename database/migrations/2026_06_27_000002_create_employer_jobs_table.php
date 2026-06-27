<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employer_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->enum('type', ['job', 'internship'])->default('job');
            $table->string('location')->nullable();
            $table->string('employment_type')->nullable(); // Full-time, Part-time, Contract, Remote
            $table->string('salary')->nullable();
            $table->string('duration')->nullable(); // For internships
            $table->text('requirements')->nullable();
            $table->text('required_skills')->nullable();
            $table->string('required_qualifications')->nullable();
            $table->string('experience_level')->nullable(); // Entry, Mid, Senior
            $table->date('deadline')->nullable();
            $table->enum('status', ['open', 'closed', 'draft'])->default('open');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employer_jobs');
    }
};
