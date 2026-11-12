<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documento_items', function (Blueprint $table) {
            $table->decimal('recibido', 12, 2)->nullable()->after('cantidad')
                ->comment('Cantidad real de mercancía recibida');
        });
    }

    public function down(): void
    {
        Schema::table('documento_items', function (Blueprint $table) {
            $table->dropColumn('recibido');
        });
    }
};
