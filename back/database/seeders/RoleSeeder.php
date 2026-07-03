<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            "name" => "SuperAdmin",
            "description" => "Super Administrator",
            "menu_ids" => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14],
            'created_admin' => true
        ]);

        Role::create([
            "name" => "admin",
            "description" => "Administrator",
            "menu_ids" => [1, 2, 3, 4, 5, 6, 7, 8, 9, 14],
            'created_admin' => true
        ]);

        Role::create([
            "name" => "Cliente",
            "description" => "Cliente",
            "menu_ids" => [15],
            'created_admin' => false
        ]);

        Role::create([
            "name" => "Trabajador",
            "description" => "Trabajador",
            "menu_ids" => [1, 2, 3, 4, 5, 8],
            'created_admin' => true
        ]);

    }
}