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
        Schema::create('class_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('virtual_class_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->dateTime('join_time');
            $table->dateTime('leave_time')->nullable();
            $table->integer('duration')->default(0); // in minutes
            $table->enum('status', ['present', 'absent'])->default('absent');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_attendances');
    }
};
