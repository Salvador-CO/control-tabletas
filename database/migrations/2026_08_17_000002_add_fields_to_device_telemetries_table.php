<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('device_telemetries', function (Blueprint $table) {
            // Mensaje masivo pendiente de mostrar en pantalla
            $table->text('pending_message')->nullable()->after('pending_command');
            // Metadatos del dispositivo
            $table->string('android_version')->nullable()->after('pending_message');
            $table->string('app_version')->nullable()->after('android_version');
            $table->string('wifi_ssid')->nullable()->after('app_version');
            $table->string('ip_address')->nullable()->after('wifi_ssid');
            $table->boolean('is_charging')->nullable()->after('ip_address');
        });
    }

    public function down(): void
    {
        Schema::table('device_telemetries', function (Blueprint $table) {
            $table->dropColumn([
                'pending_message',
                'android_version',
                'app_version',
                'wifi_ssid',
                'ip_address',
                'is_charging',
            ]);
        });
    }
};
