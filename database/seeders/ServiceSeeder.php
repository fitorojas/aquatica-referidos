<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'Bebés y Niños Principiantes',
                'slug' => 'bebes-y-ninos-principiantes',
                'regular_price' => 42000.00,
                'promo_price' => 30800.00,
                'description' => 'Programa formativo de estimulación y ambientación acuática para infantes y niños en etapa inicial.',
                'is_active' => true,
            ],
            [
                'name' => 'Niños Grandes y Adolescentes',
                'slug' => 'ninos-grandes-y-adolescentes',
                'regular_price' => 42000.00,
                'promo_price' => 30800.00,
                'description' => 'Desarrollo técnico de los cuatro estilos de natación, resistencia y seguridad en el agua.',
                'is_active' => true,
            ],
            [
                'name' => 'Adultos Principiantes',
                'slug' => 'adultos-principiantes',
                'regular_price' => 45000.00,
                'promo_price' => 30800.00,
                'description' => 'Clases guiadas para superar el temor al agua y dominar los fundamentos de la natación.',
                'is_active' => true,
            ],
            [
                'name' => 'Open Pool',
                'slug' => 'open-pool',
                'regular_price' => 38000.00,
                'promo_price' => 30800.00,
                'description' => 'Nado libre y entrenamiento continuo en carriles acondicionados.',
                'is_active' => true,
            ],
            [
                'name' => 'Aquafitness',
                'slug' => 'aquafitness',
                'regular_price' => 40000.00,
                'promo_price' => 30800.00,
                'description' => 'Entrenamiento aeróbico y de bajo impacto en el agua para tonificación y salud integral.',
                'is_active' => true,
            ],
        ];

        foreach ($services as $data) {
            Service::updateOrCreate(
                ['slug' => $data['slug']],
                $data
            );
        }
    }
}
