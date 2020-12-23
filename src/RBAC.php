<?php

namespace Rockbuzz\LaraMemberships;

use Illuminate\Support\Arr;
use Rockbuzz\LaraMemberships\Roles\NullRole;

class RBAC
{
    public static $roles = [];

    public static function createFromArray(array $roles)
    {
        static::$roles = $roles;

        array_walk(static::$roles, function (array $permissions, string $key) {
            static::role(new Role($key, $permissions));
        });
    }

    public static function role(Role $role)
    {
        static::$roles[$role->key] = $role->permissions;
    }

    public static function findRole($key): Role
    {
        return Arr::exists(static::$roles, $key) ?
            new Role($key, static::$roles[$key]) :
            new NullRole();
    }
}
