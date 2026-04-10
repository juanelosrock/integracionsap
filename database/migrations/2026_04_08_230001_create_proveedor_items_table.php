<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proveedor_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proveedor_id')->constrained('proveedores')->cascadeOnDelete();
            $table->unsignedInteger('item_id')->comment('ID del item en BD remota sapintegration');
            $table->timestamps();

            $table->unique(['proveedor_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('proveedor_items');
    }
};
