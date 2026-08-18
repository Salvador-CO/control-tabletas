<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            // Serial que reporta el dispositivo (puede diferir del serial de la caja)
            $table->string('device_reported_serial')->nullable()->unique()->after('serial_number');
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn('device_reported_serial');
        });
    }
};
