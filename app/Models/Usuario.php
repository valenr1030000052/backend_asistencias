<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Usuario extends Model
{
   
    protected $table = 'usuarios';

    protected $fillable = ['nombre', 'documento', 'codigo', 'sede_id'];

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function registros()
    {
        return $this->hasMany(Registro::class);
    }

    
}
