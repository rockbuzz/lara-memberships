<?php

namespace Rockbuzz\LaraMemberships\Roles;

use Rockbuzz\LaraMemberships\Role;

class NullRole extends Role
{
    public function __construct()
    {
        parent::__construct('');
    }
}
