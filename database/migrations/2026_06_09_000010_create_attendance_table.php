<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('training_sessions')->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('status')->default('present'); // present, absent, late, excused
            $table->string('verification_method')->default('manual'); // qr_scan, manual
            $table->timestamp('marked_at')->useCurrent();
            $table->timestamps();
            $table->unique(['session_id', 'student_id']); // Unique per session & student
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
