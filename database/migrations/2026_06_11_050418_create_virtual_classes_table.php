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
        Schema::create('virtual_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_session_id')->constrained()->onDelete('cascade');
            $table->string('room_name')->unique();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->enum('status', ['pending', 'active', 'closed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtual_classes');
    }
};
