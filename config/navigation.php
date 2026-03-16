<?php

return [

    'core' => [

        [
            'label' => 'Dashboard',
            'icon'  => 'fa-chart-line',
            'route' => 'user.dashboard',
            'roles' => ['user','admin','manager'],
        ],

        [
            'label' => 'Income',
            'icon'  => 'fa-wallet',
            'route' => 'income.index',
            'roles' => ['user','admin','manager'],
        ],

        [
            'label' => 'Expenses',
            'icon'  => 'fa-receipt',
            'route' => 'expenses.index',
            'roles' => ['user','admin','manager'],
        ],

        [
            'label' => 'Family Budget',
            'icon'  => 'fa-people-group',
            'route' => 'families.index',
            'roles' => ['user','admin','manager'],
        ],

        [
            'label' => 'Reports',
            'icon'  => 'fa-chart-pie',
            'route' => 'reports',
            'roles' => ['admin','manager'],
        ],

        [
            'label' => 'AI Assistant',
            'icon'  => 'fa-robot',
            'route' => 'ai-chat.index',
            'roles' => ['user','admin','manager'],
        ],

        [
            'label' => 'Notifications',
            'icon'  => 'fa-bell',
            'route' => 'notifications.index',
            'roles' => ['user','admin','manager'],
        ],

    ],

    'user' => [

        [
            'label' => 'Profile',
            'icon'  => 'fa-user',
            'route' => 'profile.index',
            'roles' => ['user','admin','manager'],
        ],

        [
            'label' => 'Subscription',
            'icon'  => 'fa-star',
            'route' => 'profile.subscription',
            'roles' => ['user','admin','manager'],
        ],

    ],

    'admin' => [

        [
            'label' => 'Admin Dashboard',
            'icon'  => 'fa-shield-halved',
            'route' => 'admin.dashboard',
            'roles' => ['admin'],
        ],

        [
            'label' => 'Users',
            'icon'  => 'fa-users',
            'route' => 'admin.users',
            'roles' => ['admin'],
        ],

        [
            'label' => 'Activities',
            'icon'  => 'fa-clipboard-list',
            'route' => 'admin.activities',
            'roles' => ['admin'],
        ],

    ],

];
