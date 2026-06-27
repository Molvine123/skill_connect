<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employment_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('employer_id')->constrained()->onDelete('cascade');
            $table->foreignId('employer_job_id')->nullable()->constrained()->onDelete('set null');
            $table->date('employment_date')->nullable();
            $table->enum('employment_status', [
                'hired',
                'internship_placement',
                'contract_completed',
                'offer_declined',
            ])->default('hired');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employment_records');
    }
};
