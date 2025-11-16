<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisponibilidadCoordinadora extends Model
{
    use HasFactory;

    protected $table = 'disponibilidad_coordinadora';

    protected $fillable = [
        'user_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
