<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('cv_file')->nullable()->after('phone');
            $table->text('bio')->nullable()->after('cv_file');
            $table->string('linkedin_url')->nullable()->after('bio');
            $table->string('portfolio_url')->nullable()->after('linkedin_url');
            $table->string('location')->nullable()->after('portfolio_url');
            $table->boolean('open_to_work')->default(false)->after('location');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['cv_file', 'bio', 'linkedin_url', 'portfolio_url', 'location', 'open_to_work']);
        });
    }
};
