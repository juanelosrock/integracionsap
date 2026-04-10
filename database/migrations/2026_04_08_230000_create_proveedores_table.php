<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proveedores', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_sap', 50)->unique();
            $table->string('nombre', 150);
            $table->string('nit', 30)->nullable()->comment('NIT / RUC / Identificación fiscal');
            $table->string('email')->nullable();
            $table->string('telefono', 30)->nullable();
            $table->string('direccion')->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->string('pais', 100)->nullable()->default('Colombia');
            $table->string('contacto', 100)->nullable()->comment('Nombre del contacto principal');
            $table->string('cargo_contacto', 100)->nullable();
            $table->string('telefono_contacto', 30)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proveedores');
    }
};
