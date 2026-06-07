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
        Schema::create('slips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawans')->onDelete('cascade');
            $table->string('periode_start');
            $table->string('periode_end');
            $table->decimal('total_salary', 15, 2);
            $table->decimal('total_deduction', 15, 2);
            $table->decimal('basic_salary', 15, 2);
            $table->decimal('overtime_salary', 15, 2)->default(0);
            $table->decimal('meal_allowance', 15, 2)->default(0);
            $table->decimal('transportation_allowance', 15, 2)->default(0);
            $table->decimal('bonus_salary', 15, 2)->default(0);
            $table->text('bonus_notes')->nullable();
            $table->decimal('late_deduction', 15, 2)->default(0);
            $table->decimal('absence_deduction', 15, 2)->default(0);
            $table->decimal('damaged_cost', 15, 2)->default(0);
            $table->decimal('other_deduction', 15, 2)->default(0);
            $table->text('other_deduction_notes')->nullable();
            $table->string('status')->default('DRAFT'); // PAID, DRAFT, OVERDUE, UNPAID, PARTIALLY_PAID
            $table->timestamp('paid_at')->nullable();
            $table->string('paid_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slips');
    }
};
