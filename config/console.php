<?php

return [
  'menu' => [
    'Admin' => [
      ['label'=>'Dashboard',   'route'=>'admin.dashboard'],
      ['label'=>'Submissões',  'route'=>'admin.submissions.index', 'can'=>'submissions.view'],
      ['label'=>'Edições',     'route'=>'admin.issues.index',      'can'=>'issues.view'],
      ['label'=>'Categorias',  'route'=>'admin.categories.index',  'can'=>'categories.view'],
      ['label'=>'Usuários',     'route'=>'admin.users.index',       'can'=>'users.view'],
      ['label'=>'Relatórios',  'route'=>'admin.reports.index',     'can'=>'reports.view'],
      ['label'=>'Sistema',     'route'=>'admin.system.index',      'can'=>'system.view'],
    ],

    'Coordenador' => [
    ['label'=>'Dashboard',   'route'=>'coordenador.dashboard'],
    ['label'=>'Submissões',  'route'=>'coordenador.submissions.index', 'can'=>'submissions.view'], // 👈
    ['label'=>'Revisores',   'route'=>'coordenador.revisores.index',   'can'=>'reviewers.manage'],
    ['label'=>'Relatórios',  'route'=>'admin.reports.index',           'can'=>'reports.view'],
    ],

    'Revisor' => [
      ['label'=>'Dashboard',       'route'=>'revisor.dashboard'],
      ['label'=>'Minhas revisões', 'route'=>'revisor.reviews.index', 'can'=>'reviews.view_assigned'],
    ],
  ],

  'actions' => [
    'admin.submissions.*'      => ['label'=>'+ Nova submissão','route'=>'autor.submissions.create','can'=>null],
    'admin.categories.*'       => ['label'=>'+ Categoria','route'=>'admin.categories.create','can'=>'categories.create'],
    'coordenador.revisores.*'  => ['label'=>'+ Revisor','route'=>'coordenador.revisores.create','can'=>'reviewers.manage'],
  ],
];
