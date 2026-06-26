<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('org_type')->default('private_company')->after('name'); // ngo, private_company, ajira, trainer
            $table->string('email')->nullable()->after('phone');
            $table->string('website')->nullable()->after('email');
            $table->string('address')->nullable()->after('website');
            $table->string('county')->nullable()->after('address');
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['org_type', 'email', 'website', 'address', 'county']);
        });
    }
};
