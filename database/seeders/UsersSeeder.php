<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        collect([
            ['name' => 'Administrador', 'email' => 'admin@empresa.com', 'role' => UserRole::ADMINISTRADOR],
            ['name' => 'Equipe RH', 'email' => 'rh@empresa.com', 'role' => UserRole::RH],
            ['name' => 'Equipe TI', 'email' => 'ti@empresa.com', 'role' => UserRole::TI],
            ['name' => 'Equipe Facilities', 'email' => 'facilities@empresa.com', 'role' => UserRole::FACILITIES],
            ['name' => 'Colaborador Demo', 'email' => 'colaborador@empresa.com', 'role' => UserRole::COLABORADOR],
        ])->each(fn (array $user) => User::query()->updateOrCreate(
            ['email' => $user['email']],
            [
                'name' => $user['name'],
                'role' => $user['role'],
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        ));
    }
}
