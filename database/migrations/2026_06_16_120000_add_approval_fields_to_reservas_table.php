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
        Schema::table('reservas', function (Blueprint $table): void {
            $table->foreignId('avaliado_por_id')
                ->nullable()
                ->after('status')
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('avaliado_em')->nullable()->after('avaliado_por_id');
            $table->text('motivo_reprovacao')->nullable()->after('avaliado_em');

            $table->index(['status', 'avaliado_por_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table): void {
            $table->dropIndex(['status', 'avaliado_por_id']);
            $table->dropConstrainedForeignId('avaliado_por_id');
            $table->dropColumn(['avaliado_em', 'motivo_reprovacao']);
        });
    }
};
