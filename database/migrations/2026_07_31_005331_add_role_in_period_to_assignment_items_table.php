<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assignment_items', function (Blueprint $table) {
            $table->string('role_in_period')->nullable()->after('staff_id');
        });
    }

    public function down(): void
    {
        Schema::table('assignment_items', function (Blueprint $table) {
            $table->dropColumn('role_in_period');
        });
    }
};
