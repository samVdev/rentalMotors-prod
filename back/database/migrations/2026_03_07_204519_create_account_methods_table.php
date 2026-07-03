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
        Schema::create('account_methods', function (Blueprint $table) {
            $table->id();
            
            $table->string('provider_name'); 
            
            $table->string('identifier'); 
            
            $table->enum('type', ['bank', 'wallet', 'crypto', 'other'])->default('bank');
            
            $table->string('currency', 10)->default('COP');
            
            $table->string('holder_name');
            $table->string('holder_dni')->nullable();
            $table->string('network_or_type')->nullable(); 
            
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_methods');
    }
};