<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Personas; // Asegúrate de importar el modelo

class PersonasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Personas::create([
            'fullName' => 'SUPERADMIN',
            'phone' => '987654321',
            'cedula' => 12345679,
            'direction' => 'test dir1',
            'date' => '1990-02-21',
        ]);

        Personas::create([
            'fullName' => 'Demninson Villegas',
            'phone' => '573132265632',
            'cedula' => 19650560,
            'direction' => 'test dir1',
            'date' => '1990-02-21',
        ]);

        Personas::create([
            'fullName' => 'Pablo Emilio Fgueroa de Horta',
            'phone' => '5714727981',
            'cedula' => 14727981,
            'direction' => 'test dir1',
            'date' => '1990-02-21',
        ]);

        Personas::create([
            'fullName' => 'Yondrin Zambrano',
            'phone' => '573184045494',
            'cedula' => 14727981,
            'direction' => 'test dir1',
            'date' => '1990-02-21',
        ]);

        Personas::create([
            'fullName' => 'HECTOR SANCHEZ',
            'phone' => '573024171227',
            'cedula' => 1047516298,
            'direction' => 'test dir1',
            'date' => '1990-02-21',
        ]);

        Personas::create([
            'fullName' => 'LUIS MARRUGO',
            'phone' => '573107318532',
            'cedula' => 1047517172,
            'direction' => 'test dir1',
            'date' => '1990-02-21',
        ]);
    }
}
