<?php

$themes = [
    'light' => 'Light',
    'dark' => 'Dark',
    'black' => 'Black',
    'claude' => 'Claude',
    'corporate' => 'Corporate',
    'ghibli' => 'Ghibli',
    'gourmet' => 'Gourmet',
    'luxury' => 'Luxury',
    'mintlify' => 'Mintlify',
    'pastel' => 'Pastel',
    'perplexity' => 'Perplexity',
    'shadcn' => 'Shadcn',
    'slack' => 'Slack',
    'soft' => 'Soft',
    'spotify' => 'Spotify',
    'valorant' => 'Valorant',
    'vscode' => 'VS Code',
];

$configuredTheme = strtolower(trim((string) env('APP_THEME', 'valorant')));

return [
    'default' => array_key_exists($configuredTheme, $themes) ? $configuredTheme : 'valorant',
    'storage_key' => 'laravel-skeleton-theme',
    'themes' => $themes,
];
