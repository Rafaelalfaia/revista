<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Executa os seeders da aplicação.
     */
    public function run(): void
    {

        if (class_exists(\Database\Seeders\RolesSeeder::class) && (bool) env('SEED_RUN_ROLES', false)) {
            $this->call(\Database\Seeders\RolesSeeder::class);
        }

        $this->call([
            PermissionsSeeder::class,
        ]);

        if ((bool) env('SEED_CREATE_USERS', true)) {
            $this->call([
                UsersSeeder::class,
            ]);
        }

        if ((bool) env('SEED_DOMAIN_FIXTURES', false)) {

        }
    }
}
