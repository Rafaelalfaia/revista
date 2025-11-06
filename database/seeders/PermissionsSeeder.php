<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $guard = config('auth.defaults.guard', 'web');


        $groups = [

            'submissions'            => ['view','create','update','delete','submit'],
            'submissions.triage'     => ['manage'],
            'submissions.assign'     => ['manage'],
            'submissions.decide'     => ['manage'],

            'reviews'                => ['view_assigned','submit_opinion'],
            'editions'               => ['view','manage'],
            'articles'               => ['manage','publish'],
            'categories'             => ['view','create','manage'],
            'media'                  => ['manage'],
            'reports'                => ['view'],
            'users'                  => ['view','manage'],

            'reviewers'              => ['manage'],

            'system'                 => ['view'],
        ];

        $catalog = [];
        foreach ($groups as $group => $actions) {
            foreach ($actions as $action) {
                $catalog[] = "{$group}.{$action}";
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach ($catalog as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => $guard]);
        }

        $roleAdmin       = Role::firstOrCreate(['name' => 'Admin',       'guard_name' => $guard]);
        $roleCoordenador = Role::firstOrCreate(['name' => 'Coordenador', 'guard_name' => $guard]);
        $roleRevisor     = Role::firstOrCreate(['name' => 'Revisor',     'guard_name' => $guard]);
        $roleAutor       = Role::firstOrCreate(['name' => 'Autor',       'guard_name' => $guard]);

        $allPerms = Permission::pluck('name')->all();

        $byPrefixes = function (array $prefixes) use ($allPerms): array {
            $out = [];
            foreach ($allPerms as $perm) {
                foreach ($prefixes as $p) {
                    if (Str::startsWith($perm, $p)) { $out[] = $perm; break; }
                }
            }
            return array_values(array_unique($out));
        };

        $roleAdmin->syncPermissions($allPerms);

        $roleCoordenador->syncPermissions(array_values(array_unique(array_merge(
            $byPrefixes([
                'submissions.triage.',
                'submissions.assign.',
                'submissions.decide.',
                'reviews.',
                'editions.',
                'articles.',
                'categories.',
                'media.',
            ]),
            [
                'submissions.view',
                'reports.view',
                'reviewers.manage',
            ]
        ))));

        $roleRevisor->syncPermissions($byPrefixes(['reviews.']));

        $autorAllow = [
            'submissions.view','submissions.create','submissions.update','submissions.delete','submissions.submit',
            'media.manage',
        ];
        $roleAutor->syncPermissions($autorAllow);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
