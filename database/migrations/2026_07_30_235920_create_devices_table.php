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
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('brand')->default('XIAOMI');
            $table->string('model')->default('Pad 6');
            $table->string('serial_number')->unique();
            $table->enum('status', ['disponible', 'en_resguardo', 'asignado_fijo', 'mantenimiento'])->default('disponible');
            $table->boolean('is_charged')->default(true);
            $table->string('charger_details')->nullable(); // ej: 'cargador punta', 'cargador maestro'
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
