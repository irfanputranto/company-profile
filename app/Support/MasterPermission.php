<?php

namespace App\Support;

class MasterPermission
{
    public const ACTIONS = ['view', 'create', 'show', 'delete', 'update'];

    public const RESOURCES = [
        'users',
        'roles',
        'permissions',
    ];

    public const LEGACY_PERMISSIONS = [
        'manage users',
        'manage permissions',
    ];

    /** @return list<string> */
    public static function all(): array
    {
        return collect(self::RESOURCES)
            ->flatMap(fn (string $resource): array => array_map(
                fn (string $action): string => self::name($action, $resource),
                self::ACTIONS,
            ))
            ->values()
            ->all();
    }

    public static function name(string $action, string $resource): string
    {
        return $action.'_'.$resource;
    }

    /** @param list<string> $resources
     * @return list<string>
     */
    public static function forResources(array $resources): array
    {
        return collect($resources)
            ->flatMap(fn (string $resource): array => array_map(
                fn (string $action): string => self::name($action, $resource),
                self::ACTIONS,
            ))
            ->values()
            ->all();
    }
}
