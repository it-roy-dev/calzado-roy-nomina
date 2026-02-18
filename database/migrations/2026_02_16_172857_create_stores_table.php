<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            
            // IDs de Oracle (para sincronización)
            $table->string('oracle_store_sid')->unique()->nullable(); // SID de Oracle
            $table->string('oracle_store_no')->nullable(); // STORE_NO
            $table->string('oracle_store_code')->nullable(); // STORE_CODE
            
            // Código NUEVO limpio (T-001, T-002, etc.)
            $table->string('code', 10)->unique(); // T-001, T-002, A-001, etc.
            
            // Datos de la tienda
            $table->string('name'); // Nombre
            $table->string('type', 50); // tienda, admin, temporal
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->date('activation_date')->nullable();
            
            // Estado
            $table->boolean('is_active')->default(true);
            
            // Sincronización
            $table->timestamp('last_synced_at')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};