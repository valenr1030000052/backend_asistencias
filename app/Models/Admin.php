<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
   protected $table = 'admins';

    protected $fillable = ['nombre', 'email', 'password'];

    // 🔹 Si un admin puede gestionar sedes
    public function sedes()
    {
        return $this->hasMany(Sede::class);
    }
}
