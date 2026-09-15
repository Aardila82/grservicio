<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('client_id')->constrained()->cascadeOnDelete();
            $table->string('invoice_number', 50)->unique();
            $table->text('service');
            $table->decimal('value', 12, 2);
            $table->text('notes')->nullable();
            $table->date('invoice_date');
            $table->string('pdf_path')->nullable();
            $table->enum('status', ['pending', 'sent', 'error'])->default('pending');
            $table->timestamp('whatsapp_sent_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('invoice_date');
            $table->index('client_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
