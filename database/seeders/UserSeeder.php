<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Usuario normal
        User::create([
            'name' => 'Usuario Normal',
            'email' => 'usuario@user.com',
            'password' => Hash::make('user123'),
            'tipo_usuario' => 'normal'
        ]);

        // Usuario admin
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin123'),
            'tipo_usuario' => 'admin'
        ]);
    }
}