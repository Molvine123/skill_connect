<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skill_programs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained('skill_categories')->onDelete('cascade');
            $table->string('name');
            $table->text('description');
            $table->string('duration'); // e.g. "6 Weeks", "3 Months"
            $table->decimal('cost', 10, 2)->default(0.00); // Program fee (KES)
            $table->string('mode')->default('in_person'); // online, in_person, hybrid
            $table->string('venue')->nullable(); // Physical address or online link platform
            $table->integer('capacity')->default(50);
            $table->text('requirements')->nullable(); // Prerequisites
            $table->text('learning_outcomes')->nullable(); // What student gains
            $table->string('status')->default('published'); // draft, published, closed
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('skill_programs');
    }
};
