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
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('financing_id')->nullable();
            $table->unsignedBigInteger('application_id')->nullable();
            $table->unsignedBigInteger('responsable_id')->nullable();

            $table->decimal('total', 120, 4);
            $table->enum('status', ['pending', 'completed', 'checking'])->default('pending');
            $table->text('descripcion')->nullable();
            $table->date('date')->nullable();
            $table->string('type')->nullable();

            $table->timestamps();

            $table->foreign('financing_id')
                ->references('id')
                ->on('financings')
                ->onDelete('cascade');

            $table->foreign('application_id')
                ->references('id')
                ->on('applications')
                ->onDelete('set null');

            $table->foreign('responsable_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
