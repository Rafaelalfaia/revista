<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $this->mk('Admin',       'admin@admin.com',              'admin',       'Admin');
        $this->mk('Coordenador', 'coordenador@coordenador.com',  'coordenador', 'Coordenador');
        $this->mk('Revisor',     'revisor@revisor.com',          'revisor',     'Revisor');
        $this->mk('Autor',       'autor@autor.com',              'autor',       'Autor');
    }

    protected function mk(string $nome, string $email, string $senha, string $role): array
    {
        $roleModel = Role::firstOrCreate(
            ['name' => $role, 'guard_name' => 'web']
        );

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name'              => $nome,
                'password'          => bcrypt($senha),
                'email_verified_at' => now(),
            ]
        );

        if (method_exists($user, 'syncRoles')) {
            $user->syncRoles([$roleModel->name]);
        }

        return [$user->email, $user->getRoleNames()->toArray()];
    }
}
