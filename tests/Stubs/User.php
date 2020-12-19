<?php

namespace Tests\Stubs;

use Rockbuzz\LaraMemberships\Traits\HasMemberships;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasMemberships;

    protected $guarded = [];
}
