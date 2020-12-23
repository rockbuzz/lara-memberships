<?php

namespace Tests;

use Rockbuzz\LaraMemberships\RBAC;
use Rockbuzz\LaraMemberships\Role;

class RBACTest extends TestCase
{
    /** @test */
    public function rbac_find_role()
    {
        RBAC::createFromArray([
            'admin' => ['*']
        ]);

        $this->assertEquals(
            new Role('admin', ['*']),
            RBAC::findRole('admin')
        );
    }
}
