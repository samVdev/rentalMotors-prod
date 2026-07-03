<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            'COSTO DEL GPS',
            'SERVICIO DEL GPS POR TODOS LOS MESES',
            'RENOVACION DEL GPS ANUAL',
            'TRASPASO DE PROPIEDAD',
            'COMPRA SOAT',
            'DEUDA DE SEMANA',
        ];

        foreach ($services as $serviceName) {
            Service::updateOrCreate(
                ['name' => $serviceName],
                ['description' => 'Servicio de ' . $serviceName]
            );
        }
    }
}
