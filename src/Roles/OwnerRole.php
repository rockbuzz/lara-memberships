<?php

namespace Rockbuzz\LaraMemberships\Roles;

use Rockbuzz\LaraMemberships\Role;

class OwnerRole extends Role
{
    public function __construct()
    {
        parent::__construct('owner', ['*']);
    }
}
