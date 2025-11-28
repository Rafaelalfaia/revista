<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// 🚨 NOVO IMPORT: Adicione esta linha para importar o ArtigoSeeder
use Database\Seeders\ArtigoSeeder; 

class DatabaseSeeder extends Seeder
{
    /**
     * Executa os seeders da aplicação.
     */
    public function run(): void
    {
        // Chama o RolesSeeder apenas se a classe existir e a variável de ambiente SEED_RUN_ROLES for true
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
        
        // 🚀 NOVO PASSO: Adicionando o ArtigoSeeder
        // Assumimos que ele deve rodar a menos que SEED_DOMAIN_FIXTURES esteja definido.
        // Vamos colocá-lo aqui para rodar após os usuários e permissões.
        $this->call([
            ArtigoSeeder::class,
        ]);


        if ((bool) env('SEED_DOMAIN_FIXTURES', false)) {
            // Se esta flag for usada para dados de fixtures, 
            // você pode chamar o ArtigoSeeder aqui, dependendo da sua intenção
        }
    }
}