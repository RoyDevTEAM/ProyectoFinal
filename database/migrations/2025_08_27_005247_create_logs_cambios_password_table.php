<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logs_cambios_password', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained()->onDelete('cascade');
            $table->timestamp('fecha_cambio')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logs_cambios_password');
    }
};