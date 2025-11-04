<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AsesorPedagogico extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'telefono',
    ];

    public function solicitudes(): HasMany
    {
        return $this->hasMany(Solicitud::class);
    }

    public function entrevistas(): HasMany
    {
        return $this->hasMany(Entrevista::class);
    }
}
