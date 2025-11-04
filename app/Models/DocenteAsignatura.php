<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocenteAsignatura extends Model
{
    use HasFactory;

    protected $fillable = [
        'docente_id',
        'asignatura_id',
        'carrera_id',
    ];

    public function docente(): BelongsTo
    {
        return $this->belongsTo(Docente::class);
    }

    public function asignatura(): BelongsTo
    {
        return $this->belongsTo(Asignatura::class);
    }

    public function carrera(): BelongsTo
    {
        return $this->belongsTo(Carrera::class);
    }
}
