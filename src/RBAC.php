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
    }

    public static function role(string $key, array $permissions = [])
    {
        static::$roles[$key] = $permissions;
    }

    public static function findRole($key): Role
    {
        $role = Arr::exists(static::$roles, $key);

        if ($role) {
            return new Role($key, static::$roles[$key]);
        }

        return new NullRole();
    }

    public static function clear()
    {
        static::$roles = [];
    }
}
