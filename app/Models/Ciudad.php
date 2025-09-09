<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ciudad extends Model
{

     protected $table = 'ciudades'; // 👈 Forzamos el nombre correcto
     protected $fillable = ['nombre'];

    public function sedes()
    {
        return $this->hasMany(Sede::class);
    }

    
}
