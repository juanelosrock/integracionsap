<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 20)->unique()->comment('Número auto-generado: DOC-2026-0001');
            $table->date('fecha');
            $table->foreignId('proveedor_id')->constrained('proveedores')->restrictOnDelete();
            $table->string('codigo_tienda', 50);
            $table->string('nombre_tienda', 150);
            $table->enum('estado', ['borrador', 'confirmado', 'enviado'])->default('borrador');
            $table->text('observaciones')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('documento_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documento_id')->constrained('documentos')->cascadeOnDelete();
            $table->unsignedInteger('item_id');
            $table->string('codarticulo', 50);
            $table->string('descripcion', 150);
            $table->string('unidadmedida', 50);
            $table->decimal('cantidad', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documento_items');
        Schema::dropIfExists('documentos');
    }
};
