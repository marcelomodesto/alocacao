<?php

$submenu = [
    [
        'text' => 'Usuários',
        'url' => config('app.url') . '/users',
        'can' => 'editar usuario',
    ],
    [
        'text' => 'Períodos Letivos',
        'url' => config('app.url') . '/schoolterms',
        'can' => 'visualizar periodo letivo',
    ],
    [
        'text' => 'Turmas Internas',
        'url' => config('app.url') . '/schoolclasses',
        'can' => 'visualizar turmas',
    ],
    [
        'text' => 'Turmas Externas',
        'url' => config('app.url') . '/schoolclasses/externals',
        'can' => 'visualizar turmas externas',
    ],
    [
        'text' => 'Dobradinhas',
        'url' => config('app.url') . '/fusions',
        'can' => 'visualizar dobradinhas',
    ],
    [
        'text' => 'Salas',
        'url' => config('app.url') . '/rooms',
        'can' => 'visualizar salas',
    ],
];

$menu = [
    [
        'text' => 'Horários das Disciplinas',
        'url' => config('app.url') . '/courseschedules',
    ],
];

$right_menu = [
    [
        'text' => '<i class="fas fa-cog"></i>',
        'title' => 'Configurações',
        'submenu' => $submenu,
        'align' => 'right',
        'can' => "visualizar menu config",
    ],
];


return [
    # valor default para a tag title, dentro da section title.
    # valor pode ser substituido pela aplicação.
    'title' => config('app.name'),

    # USP_THEME_SKIN deve ser colocado no .env da aplicação 
    'skin' => env('USP_THEME_SKIN', 'uspdev'),

    # chave da sessão. Troque em caso de colisão com outra variável de sessão.
    'session_key' => 'laravel-usp-theme',

    # usado na tag base, permite usar caminhos relativos nos menus e demais elementos html
    # na versão 1 era dashboard_url
    'app_url' => config('app.url'),

    # login e logout
    'logout_method' => 'POST',
    'logout_url' => 'logout',
    'login_url' => 'login',

    # menus
    'menu' => $menu,
    'right_menu' => $right_menu,
];
