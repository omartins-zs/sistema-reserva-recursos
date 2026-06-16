<?php

namespace App\Models;

use Database\Factories\HistoricoReservaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $reserva_id
 * @property string $acao
 * @property string $descricao
 * @property int|null $usuario_id
 * @property Reserva $reserva
 * @property User|null $usuario
 */
class HistoricoReserva extends Model
{
    /** @use HasFactory<HistoricoReservaFactory> */
    use HasFactory;

    protected $table = 'historico_reservas';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'reserva_id',
        'acao',
        'descricao',
        'usuario_id',
    ];

    /**
     * @return BelongsTo<Reserva, $this>
     */
    public function reserva(): BelongsTo
    {
        return $this->belongsTo(Reserva::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
