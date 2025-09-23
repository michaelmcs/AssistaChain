<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuariosTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('usuarios')->insert([
            [
                'nombre_usuario' => 'admin',
                'contrasena' => Hash::make('password'),
                'tipo_usuario' => 'administrador',
                'estado' => 'activo'
            ],
            [
                'nombre_usuario' => 'demo',
                'contrasena' => Hash::make('password'),
                'tipo_usuario' => 'empleado',
                'estado' => 'activo'
            ]
        ]);
    }
}