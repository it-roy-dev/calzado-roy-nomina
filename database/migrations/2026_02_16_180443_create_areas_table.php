<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tiendas, Administración, Temporales
            $table->string('code', 50); // TIENDAS, ADMIN, TEMPORAL
            $table->timestamps();
        });
        
        // Insertar áreas por defecto
        DB::table('areas')->insert([
            ['name' => 'Tiendas Guatemala', 'code' => 'TIENDAS', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Administración', 'code' => 'ADMIN', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Temporales/Vacacionistas', 'code' => 'TEMPORAL', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('areas');
    }
};