<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Estudiante extends Model
{
    use HasFactory;

    protected $fillable = [
        'rut',
        'nombre',
        'apellido',
        'email',
        'telefono',
        'carrera_id',
    ];

    public function carrera(): BelongsTo
    {
        return $this->belongsTo(Carrera::class);
    }

    public function solicitudes(): HasMany
    {
        return $this->hasMany(Solicitud::class);
    }

    public function ajustesRazonables(): HasMany
    {
        return $this->hasMany(AjusteRazonable::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
