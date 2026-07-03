<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vehicle;
use App\Models\Financing;
use App\Models\Payment;
use App\Models\User;
use App\Models\Lote;
use App\Models\Personas;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class FinancingTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure we have a Lote
        $lote = Lote::firstOrCreate(['nombre' => 'POS 1']);

        // Find or create admin responsible
        $admin = User::where('is_admin', true)->first() ?? User::factory()->create(['is_admin' => true, 'username' => 'admin_test']);

        if ($admin) {
            $admin->lotes()->syncWithoutDetaching([$lote->id]);
        }
        /*
                // 2. Create 1 Vehicle (13,800,000)
                $vehicle = Vehicle::create([
                    'brand' => 'Yamaha',
                    'model' => 'MT-09',
                    'year' => 2024,
                    'price' => 13800000,
                    'cc' => 890,
                    'color' => 'Icon Blue',
                    'mileage' => 0,
                    'type' => 'bike',
                    'show' => true,
                    'user_id' => $admin->id,
                    'image' => 'https://images.unsplash.com/photo-1558981852-426c6c22a4d6?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80'
                ]);

                $plans = [
                    [
                        'plan' => 'Diario',
                        'price_diario' => 50000,
                        'installments' => 3,
                        'code' => 'S-DIARIO',
                        'name' => 'Cliente Plan Diario'
                    ],
                    [
                        'plan' => 'Semanal',
                        'price_semanal' => 250000,
                        'installments' => 3,
                        'code' => 'S-SEMANAL',
                        'name' => 'Cliente Plan Semanal'
                    ],
                    [
                        'plan' => 'Quincenal',
                        'price_quincenal' => 500000,
                        'installments' => 3,
                        'code' => 'S-QUINCENAL',
                        'name' => 'Cliente Plan Quincenal'
                    ],
                    [
                        'plan' => 'Mensual',
                        'price_mensual' => 1000000,
                        'installments' => 3,
                        'code' => 'S-MENSUAL',
                        'name' => 'Cliente Plan Mensual'
                    ],
                ];

                foreach ($plans as $p) {
                    // Create a unique client for each plan
                    $persona = Personas::create([
                        'fullName' => $p['name'],
                        'phone' => '584129843277',
                        'cedula' => rand(10000000, 99999999),
                        'direction' => 'Direccion ' . $p['plan'],
                        'date' => '1990-01-01',
                    ]);

                    $client = User::create([
                        'username' => 'user_' . strtolower($p['plan']),
                        'email' => strtolower($p['plan']) . '@test.com',
                        'password' => Hash::make('12345678'),
                        'persona_id' => $persona->id,
                        'suspend' => false,
                        'role_id' => 3
                    ]);

                    if ($client) {
                        $client->lotes()->syncWithoutDetaching([$lote->id]);
                    }

                    // Set start date to demonstrate pro-rata logic
                    // Weekly: Set to previous Friday (for Saturday payment)
                    // Quincenal: Set to the 11th of the previous month (for 15th payment)
                    // Mensual: Set to the 20th of the previous month (for 30th payment)
                    $startDate = match ($p['plan']) {
                        'Semanal' => Carbon::parse('last friday'),
                        'Quincenal' => Carbon::now()->subMonth()->day(11),
                        'Mensual' => Carbon::now()->subMonth()->day(20),
                        'Diario' => Carbon::now()->subDays(5),
                        default => Carbon::now()->subDays(2),
                    };

                    Financing::create([
                        'code' => $p['code'],
                        'plan' => $p['plan'],
                        'vehicle_id' => $vehicle->id,
                        'user_id' => $client->id,
                        'responsable_id' => $admin->id,
                        'status' => 'active',
                        'type' => 'vehicle',
                        'payment_initial' => 4000000,
                        'cost_price' => 13800000,
                        'installments' => $p['installments'],
                        'start_date' => $startDate->format('Y-m-d'),
                        'price_diario' => $p['price_diario'] ?? 0,
                        'price_semanal' => $p['price_semanal'] ?? 0,
                        'price_quincenal' => $p['price_quincenal'] ?? 0,
                        'price_mensual' => $p['price_mensual'] ?? 0,
                        'lote_id' => $lote->id,
                        'moraStatus' => true
                    ]);

                }*/
    }
}