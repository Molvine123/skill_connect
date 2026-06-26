<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->string('type')->default('university')->after('name'); // university, college, tvet
            $table->string('email')->nullable()->after('phone');
            $table->string('website')->nullable()->after('email');
            $table->string('county')->nullable()->after('location');
            $table->text('description')->nullable()->after('website');
        });
    }

    public function down(): void
    {
        Schema::table('institutions', function (Blueprint $table) {
            $table->dropColumn(['type', 'email', 'website', 'county', 'description']);
        });
    }
};
