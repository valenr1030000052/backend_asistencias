<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
      protected $table = 'sedes';

    protected $fillable = ['nombre', 'ciudad_id'];

    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class);
    }

    public function usuarios()
    {
        return $this->hasMany(Usuario::class);
    }
}
