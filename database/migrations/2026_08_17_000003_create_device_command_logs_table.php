<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('device_command_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained()->onDelete('cascade');
            $table->string('command');
            $table->text('payload')->nullable(); // JSON adicional (ej. el mensaje enviado)
            $table->string('sent_by')->nullable(); // usuario que envió el comando
            $table->timestamp('sent_at')->useCurrent();
            $table->timestamp('executed_at')->nullable(); // cuando el dispositivo lo ejecutó
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_command_logs');
    }
};
