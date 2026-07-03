<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Menu::create(["title" => "Dashboard", "menu_id" => null, "path" => "dashboard", "icon" => "fas fa-home", "sort" => 1]);

        Menu::create(["title" => "Solicitudes", "menu_id" => null, "path" => "view-apply", "icon" => "fa-solid fa-inbox", "sort" => 2]);

        Menu::create(["title" => "Clientes", "menu_id" => null, "path" => "clients", "icon" => "fa-solid fa-user-group", "sort" => 3]);

        Menu::create(["title" => "financiación", "menu_id" => null, "path" => "financing", "icon" => "fa-solid fa-coins", "sort" => 4]);

        Menu::create(["title" => "Cobros", "menu_id" => null, "path" => "cobros", "icon" => "fa-solid fa-money-bill-trend-up", "sort" => 5]);

        Menu::create(["title" => "Pagos", "menu_id" => null, "path" => "payments", "icon" => "fa-solid fa-comments-dollar", "sort" => 6]);

        Menu::create(["title" => "Motos", "menu_id" => null, "path" => "vehicles/bikes", "icon" => "fa-solid fa-motorcycle", "sort" => 7]);

        Menu::create(["title" => "Carros", "menu_id" => null, "path" => "vehicles/cars", "icon" => "fa-solid fa-car", "sort" => 8]);

        Menu::create(["title" => "Mantenimiento", "menu_id" => null, "path" => "maintenance", "icon" => "fa-solid fa-hammer", "sort" => 9]);

        Menu::create(["title" => "Usuarios", "menu_id" => null, "path" => "users", "icon" => "user", "sort" => 10]);

        Menu::create(["title" => "Lotes", "menu_id" => null, "path" => "lotes", "icon" => "warehouse", "sort" => 11]);

        Menu::create(["title" => "Menus", "menu_id" => null, "path" => "menus", "icon" => "list", "sort" => 12]);

        Menu::create(["title" => "Roles", "menu_id" => null, "path" => "roles", "icon" => "user-secret", "sort" => 13]);

        Menu::create(["title" => "Métodos de Pago", "menu_id" => null, "path" => "account-methods", "icon" => "wallet", "sort" => 14]);


        // seed guest
        Menu::create(["title" => "Home", "menu_id" => null, "path" => "home", "icon" => "fas fa-home", "sort" => 1]);

    }
}