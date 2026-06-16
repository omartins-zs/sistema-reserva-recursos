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
        Schema::create('reservas', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('recurso_id')->constrained('recursos')->cascadeOnDelete();
            $table->string('solicitante_nome');
            $table->string('solicitante_email')->index();
            $table->string('departamento')->index();
            $table->text('motivo');
            $table->text('participantes')->nullable();
            $table->date('data_reserva')->index();
            $table->time('hora_inicio');
            $table->time('hora_fim');
            $table->string('status')->default('confirmado')->index();
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->index(['recurso_id', 'data_reserva', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
