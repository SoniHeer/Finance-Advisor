<?php

return [

    'core' => [
        [
            'label' => 'Dashboard',
            'route' => 'user.dashboard',
            'icon'  => 'fa-chart-line',
            'roles' => ['user','admin','manager']
        ],
        [
            'label' => 'Income',
            'route' => 'income.index',
            'icon'  => 'fa-wallet',
            'roles' => ['user','admin','manager']
        ],
        [
            'label' => 'Expenses',
            'route' => 'expenses.index',
            'icon'  => 'fa-receipt',
            'roles' => ['user','admin','manager']
        ],
        [
            'label' => 'Family Budget',
            'route' => 'families.index',
            'icon'  => 'fa-people-group',
            'roles' => ['user','admin','manager']
        ],
    ],

    'admin' => [
        [
            'label' => 'Admin Dashboard',
            'route' => 'admin.dashboard',
            'icon'  => 'fa-shield-halved',
            'roles' => ['admin']
        ],
        [
            'label' => 'Users',
            'route' => 'admin.users',
            'icon'  => 'fa-users',
            'roles' => ['admin']
        ],
        [
            'label' => 'Activities',
            'route' => 'admin.activities',
            'icon'  => 'fa-clipboard-list',
            'roles' => ['admin']
        ],
    ],

];
