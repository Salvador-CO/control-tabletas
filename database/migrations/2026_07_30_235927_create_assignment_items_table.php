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
        Schema::create('assignment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->onDelete('cascade');
            $table->foreignId('device_id')->constrained();
            $table->foreignId('staff_id')->nullable()->constrained('staff'); // Personal asignado específico
            $table->boolean('has_case_strap')->default(true); // Funda y Correa
            $table->boolean('is_returned')->default(false); // Palomita de Liberación
            $table->timestamp('returned_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_items');
    }
};
