<?php

return [
    'public_routes' => [
        'home',
        'localized-home',
        'projects.index',
        'blog.index',
        'blog.show',
        'pricing.index',
    ],

    'events' => [
        'menu' => [
            'home' => 1,
            'services' => 2,
            'projects' => 3,
            'blog' => 4,
            'pricing' => 5,
            'contact' => 6,
        ],
        'section' => [
            'home' => 1,
            'services' => 2,
            'projects' => 3,
            'contact' => 4,
        ],
    ],
];
