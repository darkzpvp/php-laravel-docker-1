<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SuscripcionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = \App\Models\User::first(); // Asumiendo que ya tienes al menos un usuario registrado

        DB::table('suscripciones')->insert([
            ['user_id' => $user->id, 'tipo' => 'basico', 'prompts_disponibles' => 10, 'precio' => 9],
            ['user_id' => $user->id, 'tipo' => 'estandar', 'prompts_disponibles' => 25, 'precio' => 19],
            ['user_id' => $user->id, 'tipo' => 'premium', 'prompts_disponibles' => 9999, 'precio' => 25]
        ]);
    }
}
