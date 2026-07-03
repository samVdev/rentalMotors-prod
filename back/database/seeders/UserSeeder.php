<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {

    \App\Models\User::factory(1)->create(
    [
      'username' => 'superadmin',
      'email' => 'user@example.com',
      'suspend' => false,
      'persona_id' => 1,
      'email_verified_at' => null,
      'is_admin' => true,
      'role_id' => 1
    ]
    );

    \App\Models\User::factory(1)->create(
    [
      'username' => 'Dvillegas',
      'email' => 'Dvillegas@gmail.com',
      'suspend' => false,
      'persona_id' => 2,
      'email_verified_at' => null,
      'password' => \Illuminate\Support\Facades\Hash::make('12345678'),
      'is_admin' => true,
      'role_id' => 4
    ]
    );

    \App\Models\User::factory(1)->create(
    [
      'username' => 'PFigueroa',
      'email' => 'Pfgueroa@gmail.com',
      'suspend' => false,
      'persona_id' => 3,
      'email_verified_at' => null,
      'password' => \Illuminate\Support\Facades\Hash::make('12345678'),
      'is_admin' => true,
      'role_id' => 4
    ]
    );

    \App\Models\User::factory(1)->create(
    [
      'username' => 'Yzambrano',
      'email' => 'Yzambrano@gmail.com',
      'suspend' => false,
      'persona_id' => 4,
      'email_verified_at' => null,
      'password' => \Illuminate\Support\Facades\Hash::make('12345678'),
      'is_admin' => true,
      'role_id' => 4
    ]
    );

    \App\Models\User::factory(1)->create(
    [
      'username' => 'Hsanchez',
      'email' => 'Hsanchez@gmail.com',
      'suspend' => false,
      'persona_id' => 5,
      'email_verified_at' => null,
      'is_admin' => true,
      'password' => \Illuminate\Support\Facades\Hash::make('12345678'),
      'role_id' => 2
    ]
    );

    \App\Models\User::factory(1)->create(
    [
      'username' => 'Lmarrugo',
      'email' => 'Lmarrugo@gmail.com',
      'suspend' => false,
      'persona_id' => 6,
      'email_verified_at' => null,
      'password' => \Illuminate\Support\Facades\Hash::make('12345678'),
      'is_admin' => true,
      'role_id' => 1
    ]
    );
  }
}