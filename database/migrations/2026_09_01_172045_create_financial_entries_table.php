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
        Schema::create('financial_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained('people')->restrictOnDelete();
            $table->string('type', 20); // payable, receivable
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->date('issue_date');
            $table->date('due_date');
            $table->date('settled_at')->nullable(); // Data de pagamento ou recebimento
            $table->string('status', 20)->default('pending'); // pending, paid, overdue, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['type', 'status']);
            $table->index('due_date');
            $table->index('issue_date');
            $table->index('settled_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_entries');
    }
};
