<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Registro extends Model
{
     protected $fillable = ['usuario_id', 'sede_id', 'hora_entrada', 'hora_salida'];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }
}
