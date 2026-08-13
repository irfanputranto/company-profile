<?php

return [
    'error_center' => 'Recovery center',
    'status' => 'Status',
    'back_home' => 'Back to home',
    'try_again' => 'Try again',
    'reassurance' => 'Rest assured, your data remains safe.',
    'pages' => [
        '401' => [
            'title' => 'We need to recognize you',
            'message' => 'We could not recognize this session. Return home or sign in again to continue your journey.',
        ],
        '402' => [
            'title' => 'This step requires payment',
            'message' => 'The request cannot continue until the required payment has been completed.',
        ],
        '403' => [
            'title' => 'This area is locked',
            'message' => 'You do not have permission to open this page. Return home to choose another available path.',
        ],
        '404' => [
            'title' => 'This page drifted into another dimension',
            'message' => 'The link you followed may have moved, changed its name, or never existed.',
        ],
        '419' => [
            'title' => 'This session has fallen asleep',
            'message' => 'For your security, sessions expire after being inactive. Reload the page and try once more.',
        ],
        '429' => [
            'title' => 'Requests are arriving too quickly',
            'message' => 'The system is catching its breath. Wait a moment, then try again.',
        ],
        '500' => [
            'title' => 'A disturbance reached mission control',
            'message' => 'Something unexpected happened on our side. Our system team can investigate while you try again.',
        ],
        '503' => [
            'title' => 'The service is recharging',
            'message' => 'We are performing brief maintenance to get everything back in shape. Please return shortly.',
        ],
        '4xx' => [
            'title' => 'This request cannot continue yet',
            'message' => 'Part of this request could not be processed. Return home and try another path.',
        ],
        '5xx' => [
            'title' => 'The system is recovering',
            'message' => 'The service encountered a temporary issue. Please try again in a few moments.',
        ],
    ],
];
