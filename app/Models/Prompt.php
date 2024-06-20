<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prompt extends Model
{
    protected $fillable = ['user_id', 'texto'];

    /**
     * Define la relación con el usuario.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
