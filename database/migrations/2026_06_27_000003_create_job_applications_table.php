<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employer_job_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->text('cover_letter')->nullable();
            $table->string('cv_file')->nullable();
            $table->enum('status', [
                'submitted',
                'under_review',
                'shortlisted',
                'interview_scheduled',
                'hired',
                'rejected'
            ])->default('submitted');
            $table->timestamp('applied_at')->useCurrent();
            $table->timestamps();

            $table->unique(['employer_job_id', 'student_id']); // Prevent duplicate applications
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
