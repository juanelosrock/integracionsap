<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gmail_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->text('access_token');
            $table->text('refresh_token')->nullable();
            $table->integer('expires_in')->nullable();
            $table->timestamp('token_created_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gmail_tokens');
    }
};
