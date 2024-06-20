<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class InformacionPersonal extends Model
{
    use HasFactory;
    protected $table = 'informacion_personal';


    protected $fillable = [
        'user_id',
        'nombre',
        'apellidos',
        'numero_telefono',
        'pais',
        'poblacion',
        'provincia',
        'nif_nie',
        'direccion',
        'cp'
    ];

    // Define la relación con el modelo User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
