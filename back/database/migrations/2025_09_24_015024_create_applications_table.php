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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('full_name');
            $table->string('phone');
            $table->string('cedula');
            $table->string('direccion', 255);
            $table->enum('type', ['financing','cash', 'mantenence']);
            $table->enum('plan', ['Diario','Semanal','Quincenal','Mensual'])->nullable();
            $table->text('folder');

            // Archivos y estado
            $table->text('document_pdf')->nullable();
            $table->enum('status', ['pending', 'accept', 'reject'])->default('pending');
            $table->timestamps();

            // 🔹 Relaciones
            $table->foreign('vehicle_id')->references('id')->on('vehicles');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
