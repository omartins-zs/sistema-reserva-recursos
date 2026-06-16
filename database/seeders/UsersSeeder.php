<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Departamento;
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
        $departamentos = Departamento::query()->get()->keyBy('nome');

        collect([
            ['name' => 'Administrador', 'email' => 'admin@empresa.com', 'role' => UserRole::ADMINISTRADOR, 'departamento' => 'Diretoria'],
            ['name' => 'Equipe RH', 'email' => 'rh@empresa.com', 'role' => UserRole::RH, 'departamento' => 'RH'],
            ['name' => 'Equipe TI', 'email' => 'ti@empresa.com', 'role' => UserRole::TI, 'departamento' => 'TI'],
            ['name' => 'Equipe Facilities', 'email' => 'facilities@empresa.com', 'role' => UserRole::FACILITIES, 'departamento' => 'Facilities'],
            ['name' => 'Gestor Financeiro', 'email' => 'financeiro@empresa.com', 'role' => UserRole::COLABORADOR, 'departamento' => 'Financeiro'],
            ['name' => 'Gestor Contabilidade', 'email' => 'contabilidade@empresa.com', 'role' => UserRole::COLABORADOR, 'departamento' => 'Contabilidade'],
            ['name' => 'Gestor Comercial', 'email' => 'comercial@empresa.com', 'role' => UserRole::COLABORADOR, 'departamento' => 'Comercial'],
            ['name' => 'Gestor Compras', 'email' => 'compras@empresa.com', 'role' => UserRole::COLABORADOR, 'departamento' => 'Compras'],
            ['name' => 'Gestor Marketing', 'email' => 'marketing@empresa.com', 'role' => UserRole::COLABORADOR, 'departamento' => 'Marketing'],
            ['name' => 'Colaborador Demo', 'email' => 'colaborador@empresa.com', 'role' => UserRole::COLABORADOR, 'departamento' => 'Administrativo'],
        ])->each(function (array $user) use ($departamentos): void {
            $departamento = $departamentos->get($user['departamento']);

            User::query()->updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'role' => $user['role'],
                    'departamento_id' => $departamento?->id,
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ],
            );
        });

        collect([
            'RH' => 'rh@empresa.com',
            'TI' => 'ti@empresa.com',
            'Facilities' => 'facilities@empresa.com',
            'Financeiro' => 'financeiro@empresa.com',
            'Contabilidade' => 'contabilidade@empresa.com',
            'Comercial' => 'comercial@empresa.com',
            'Compras' => 'compras@empresa.com',
            'Marketing' => 'marketing@empresa.com',
            'Administrativo' => 'admin@empresa.com',
            'Diretoria' => 'admin@empresa.com',
        ])->each(function (string $email, string $departamentoNome) use ($departamentos): void {
            $departamento = $departamentos->get($departamentoNome);
            $gestor = User::query()->where('email', $email)->first();

            if (! $departamento || ! $gestor) {
                return;
            }

            $departamento->update([
                'gestor_user_id' => $gestor->id,
            ]);
        });
    }
}
