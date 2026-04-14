<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            $table->integer('sap_http_code')->nullable()->after('estado');
            $table->json('sap_respuesta')->nullable()->after('sap_http_code');
            $table->timestamp('sap_enviado_at')->nullable()->after('sap_respuesta');
        });
    }

    public function down(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            $table->dropColumn(['sap_http_code', 'sap_respuesta', 'sap_enviado_at']);
        });
    }
};
