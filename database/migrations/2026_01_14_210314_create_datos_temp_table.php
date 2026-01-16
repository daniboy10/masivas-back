<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('datos_temp', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('paterno', 100);
            $table->string('materno', 100)->nullable();
            $table->string('telefono', 20);
            $table->string('calle', 200);
            $table->string('numero_exterior', 20);
            $table->string('numero_interior', 20)->nullable();
            $table->string('colonia', 100);
            $table->string('cp', 10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datos_temp');
    }
};