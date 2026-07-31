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
         
            Schema::create('assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('event_id')->nullable()->constrained();
                $table->foreignId('location_id')->nullable()->constrained();
                $table->foreignId('coordinator_id')->constrained('staff');
                $table->string('delivery_person_name')->default('MARCELA PEÑA ORDOÑEZ');
                $table->integer('chargers_count')->default(0);
                $table->date('start_date');
                $table->date('end_date');
                $table->enum('status', ['activo', 'completado', 'cancelado'])->default('activo');
                $table->text('observations')->nullable();
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
