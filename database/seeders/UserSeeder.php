<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Administrador Principal
        User::updateOrCreate(
            ['email' => 'admin@aquatica.cr'],
            [
                'name' => 'Administrador AQUATICA',
                'password' => Hash::make('AdminAquatica2026!'),
                'role' => 'super_admin',
                'is_active' => true,
            ]
        );

        // Agente Comercial / Recepción
        User::updateOrCreate(
            ['email' => 'recepcion@aquatica.cr'],
            [
                'name' => 'Recepción AQUATICA',
                'password' => Hash::make('RecepcionAquatica2026!'),
                'role' => 'receptionist',
                'is_active' => true,
            ]
        );
    }
}
