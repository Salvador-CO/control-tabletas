<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permanent_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained()->onDelete('cascade');
            $table->foreignId('staff_id')->constrained('staff')->onDelete('cascade');
            $table->string('role');                          // Cargo en esta asignación
            $table->date('assigned_date');                   // Desde cuándo tiene el equipo
            $table->text('notes')->nullable();               // Estado físico, accesorios, etc.
            $table->date('released_date')->nullable();       // Null = sigue activo
            $table->string('released_reason')->nullable();   // Motivo de liberación
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permanent_assignments');
    }
};
