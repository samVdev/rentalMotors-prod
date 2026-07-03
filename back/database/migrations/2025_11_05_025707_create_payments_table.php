<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('financing_id');
            $table->foreign('financing_id')
                ->references('id')
                ->on('financings')
                ->onDelete('cascade');

            $table->integer('installment_number');
            $table->decimal('total', 12, 2);
            $table->decimal('total_capital', 12, 2)->nullable();
            $table->decimal('total_interes', 12, 2)->nullable();

            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('file_path')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
