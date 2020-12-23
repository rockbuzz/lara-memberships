<?php

namespace Tests;

use Rockbuzz\LaraMemberships\Role;

class RoleTest extends TestCase
{
    /** @test */
    public function role_json_serialize()
    {
        $role = new Role('admin', ['*']);

        $this->assertEquals(
            [
                'key' => 'admin',
                'permissions' => ['*']
            ],
            $role->jsonSerialize()
        );
    }
}
