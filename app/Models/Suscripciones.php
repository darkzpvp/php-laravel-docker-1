<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Suscripciones extends Model
{
    use HasFactory;

    protected $table = 'suscripciones';

    protected $fillable = [
        'user_id',
        'tipo',
        'prompts_disponibles',
        'precio',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}