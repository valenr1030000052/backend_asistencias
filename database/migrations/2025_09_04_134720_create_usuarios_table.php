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
        Schema::create('usuarios', function (Blueprint $table) {
        $table->id();
        $table->string('nombre');
        $table->string('documento')->unique(); // 👈 Documento obligatorio y único
        $table->string('codigo_barras')->unique(); // Código de carnet
        $table->foreignId('sede_id')->constrained('sedes')->onDelete('cascade');
        $table->timestamps();

              
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
