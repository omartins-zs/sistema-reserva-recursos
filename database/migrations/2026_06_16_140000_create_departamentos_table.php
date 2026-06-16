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
        Schema::create('departamentos', function (Blueprint $table): void {
            $table->id();
            $table->string('nome')->unique();
            $table->string('sigla', 20)->nullable();
            $table->text('descricao')->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->foreignId('departamento_id')
                ->nullable()
                ->after('role')
                ->constrained('departamentos')
                ->nullOnDelete();
        });

        Schema::table('reservas', function (Blueprint $table): void {
            $table->foreignId('departamento_id')
                ->nullable()
                ->after('solicitante_email')
                ->constrained('departamentos')
                ->nullOnDelete();
        });

        Schema::table('departamentos', function (Blueprint $table): void {
            $table->foreignId('gestor_user_id')
                ->nullable()
                ->after('ativo')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departamentos', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('gestor_user_id');
        });

        Schema::table('reservas', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('departamento_id');
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('departamento_id');
        });

        Schema::dropIfExists('departamentos');
    }
};
