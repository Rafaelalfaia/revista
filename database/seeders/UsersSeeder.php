<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('SEED_ADMIN_EMAIL', 'admin@trivento.local');
        $admin = User::updateOrCreate(
            ['email' => $email],
            ['name' => 'Administrador', 'password' => Hash::make(env('SEED_ADMIN_PASSWORD','password'))]
        );
        if (method_exists($admin, 'assignRole')) $admin->assignRole('Admin');


        $coord = User::updateOrCreate(
            ['email' => 'coord@trivento.local'],
            ['name' => 'Coord', 'password' => Hash::make('password')]
        );
        $coord->assignRole('Coordenador');

        $rev = User::updateOrCreate(
            ['email' => 'revisor@trivento.local'],
            ['name' => 'Revisor', 'password' => Hash::make('password')]
        );
        $rev->assignRole('Revisor');

        $autor = User::updateOrCreate(
            ['email' => 'autor@trivento.local'],
            ['name' => 'Autor', 'password' => Hash::make('password')]
        );
        $autor->assignRole('Autor');
    }
}
