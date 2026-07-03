<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('financings', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('application_id')->nullable();
            $table->unsignedBigInteger('vehicle_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('responsable_id')->nullable();
            $table->unsignedBigInteger('lote_id')->nullable();

            $table->string('code')->nullable();
            $table->integer('months')->nullable();
            $table->enum('plan', ['Diario', 'Semanal', 'Quincenal', 'Mensual']);
            $table->enum('type', ['vehicle', 'tax', 'mantenence']);
            $table->integer('installments')->nullable(); // cuotas
            $table->date('start_date')->nullable();
            $table->string('plate', 20)->nullable()->unique();
            $table->text('observation')->nullable();

            $table->decimal('interes_porcent', 120, 3)->nullable();

            $table->text('payment_initial')->nullable();
            $table->decimal('deuda_adquirida', 120, 4)->nullable();
            $table->decimal('total_inicial', 120, 4)->nullable();

            $table->decimal('cost_price', 120, 4)->nullable();
            $table->decimal('financing_price', 120, 4)->nullable();
            $table->decimal('interes_price', 120, 4)->nullable();
            $table->decimal('final_price', 120, 4)->nullable();

            $table->decimal('price_diario', 120, 4)->nullable();
            $table->decimal('price_semanal', 120, 4)->nullable();
            $table->decimal('price_quincenal', 120, 4)->nullable();
            $table->decimal('price_mensual', 120, 4)->nullable();

            $table->boolean('moraStatus')->default(true);

            // Estado del financiamiento
            $table->enum('status', ['active', 'cancelled', 'finished', 'pending'])->default('pending');

            $table->timestamp('last_whatsapp_sent')->nullable();

            $table->timestamps();

            // 🔹 Relaciones
            $table->foreign('application_id')->references('id')->on('applications')->onDelete('set null');
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('responsable_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('lote_id')->references('id')->on('lotes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financings');
    }
};