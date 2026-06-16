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
        Schema::create('recursos', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tipo_recurso_id')->constrained('tipos_recursos')->cascadeOnDelete();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->string('codigo_patrimonio')->nullable()->index();
            $table->string('localizacao')->nullable();
            $table->unsignedInteger('capacidade')->nullable();
            $table->string('placa')->nullable()->index();
            $table->string('modelo')->nullable();
            $table->string('marca')->nullable();
            $table->string('status')->default('disponivel')->index();
            $table->boolean('ativo')->default(true)->index();
            $table->timestamps();

            $table->index(['tipo_recurso_id', 'status', 'ativo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recursos');
    }
};
