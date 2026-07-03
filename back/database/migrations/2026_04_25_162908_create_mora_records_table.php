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
        Schema::create('mora_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('financing_id');
            $table->double('base_amount');
            $table->double('reconnection_fee')->default(5000);
            $table->double('total_amount');
            $table->integer('percentage');
            $table->integer('occurrence_index');
            $table->enum('status', ['pending', 'paid', 'approved', 'rejected'])->default('pending');
            $table->timestamps();

            $table->foreign('financing_id')->references('id')->on('financings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mora_records');
    }
};
