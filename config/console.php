<?php

return [
  'menu' => [
    'Admin' => [
      ['label'=>'Dashboard',   'route'=>'admin.dashboard'],
      ['label'=>'Submissões',  'route'=>'admin.submissions.index', 'can'=>'submissions.view'],
      ['label'=>'Edições',     'route'=>'admin.editions.index',    'can'=>'editions.view'],
      ['label'=>'Categorias',  'route'=>'admin.categories.index',  'can'=>'categories.view'],
      ['label'=>'Usuários',    'route'=>'admin.users.index',       'can'=>'users.view'],
      ['label'=>'Relatórios',  'route'=>'admin.reports.index',     'can'=>'reports.view'],
      ['label'=>'Perfil',      'route'=>'profile.edit'],
    ],

    'Coordenador' => [
      ['label'=>'Dashboard',   'route'=>'coordenador.dashboard'],
      ['label'=>'Submissões',  'route'=>'coordenador.submissions.index', 'can'=>'submissions.view'],
      ['label'=>'Revisores',   'route'=>'coordenador.revisores.index',   'can'=>'reviewers.manage'],
      ['label'=>'Relatórios',  'route'=>'coordenador.relatorios.revisores.index','can'=>'reports.view'],
      ['label'=>'Perfil',      'route'=>'profile.edit'],
    ],

    'Revisor' => [
      ['label'=>'Dashboard',       'route'=>'revisor.dashboard'],
      ['label'=>'Minhas revisões', 'route'=>'revisor.reviews.index', 'can'=>'reviews.view_assigned'],
      ['label'=>'Perfil',          'route'=>'profile.edit'],
    ],
  ],

  'actions' => [
    'admin.submissions.*'      => ['label'=>'+ Nova submissão','route'=>'autor.submissions.create','can'=>null],
    'admin.categories.*'       => ['label'=>'+ Categoria','route'=>'admin.categories.create','can'=>'categories.create'],
    'admin.editions.*'         => ['label'=>'+ Edição','route'=>'admin.editions.create','can'=>'editions.create'],
    'coordenador.revisores.*'  => ['label'=>'+ Revisor','route'=>'coordenador.revisores.create','can'=>'reviewers.manage'],
  ],
];
