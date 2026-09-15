<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('company_name');
            $table->string('company_phone', 30)->nullable();
            $table->string('company_email')->nullable();
            $table->text('company_address')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('currency', 10)->default('COP');
            $table->text('whatsapp_message')->nullable();
            $table->string('invoice_prefix', 10)->default('FAC');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
