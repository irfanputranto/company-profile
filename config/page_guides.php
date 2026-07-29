<?php

return [
    'local_routes' => [],

    'fallback' => [
        'key' => 'fallback',
        'icon' => 'icon-[tabler--help-circle]',
    ],

    'pages' => [
        [
            'routes' => ['dashboard'],
            'key' => 'dashboard',
            'icon' => 'icon-[tabler--layout-dashboard]',
        ],
        [
            'routes' => ['profile'],
            'key' => 'profile',
            'icon' => 'icon-[tabler--user-circle]',
        ],
        [
            'routes' => ['master.users.*'],
            'key' => 'users',
            'icon' => 'icon-[tabler--users]',
        ],
        [
            'routes' => ['master.roles.*', 'master.permissions.*'],
            'key' => 'access',
            'icon' => 'icon-[tabler--user-shield]',
        ],
        [
            'routes' => ['system.activity-logs.*'],
            'key' => 'activity_log',
            'icon' => 'icon-[tabler--activity]',
        ],
    ],
];
