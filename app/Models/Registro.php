<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registro extends Model
{
    protected $table = 'registros'; // 👈 asegúrate que coincida con tu tabla real

    protected $fillable = [
        'usuario_id',
        'sede_id',
        'hora_entrada',
        'hora_salida'
    ];

    // 👇 Para que Laravel maneje bien las fechas
    protected $casts = [
        'hora_entrada' => 'datetime',
        'hora_salida'  => 'datetime',
    ];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }
}