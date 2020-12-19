<?php

namespace Rockbuzz\LaraMemberships;

use JsonSerializable;

class Role implements JsonSerializable
{
    public $key;

    public $permissions;

    public function __construct(string $key, array $permissions = [])
    {
        $this->key = $key;
        $this->permissions = $permissions;
    }
    
    public function jsonSerialize()
    {
        return [
            'key' => $this->key,
            'permissions' => $this->permissions
        ];
    }
}
