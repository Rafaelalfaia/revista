<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsPatchSeeder extends Seeder
{
    public function run(): void
    {
        $perms = [
            'submissions.read_all',
            'submissions.edit_any',
            'suggestions.create',
            'suggestions.apply',
            'suggestions.moderate',
            'comments.author_resolve',
            'comments.verify',
        ];

        foreach ($perms as $p) Permission::findOrCreate($p);

        $admin = Role::findByName('Admin');
        $admin->givePermissionTo($perms);

        $revisor = Role::findByName('Revisor');
        $revisor->givePermissionTo(['suggestions.create','comments.verify']);


        $autor = Role::findByName('Autor');
        if ($autor) $autor->givePermissionTo(['comments.author_resolve']);
    }
}
