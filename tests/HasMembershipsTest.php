<?php

namespace Tests;

use Tests\Stubs\User;
use Rockbuzz\LaraMemberships\Role;
use Illuminate\Support\Facades\DB;
use Rockbuzz\LaraMemberships\RBAC;
use Rockbuzz\LaraMemberships\Models\Account;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Rockbuzz\LaraMemberships\Roles\{NullRole, OwnerRole};

class HasMembershipsTest extends TestCase
{
    /** @test */
    public function a_user_can_have_owned_accounts()
    {
        $user = $this->create(User::class);
        $account = $this->create(Account::class, [
            'user_id' => $user->id
        ]);

        $this->assertInstanceOf(HasMany::class, $user->ownedAccounts());
        $this->assertContains($account->id, $user->ownedAccounts()->pluck('id'));
    }

    /** @test */
    public function a_user_can_have_all_accounts()
    {
        $user = $this->create(User::class);
        $account = $this->create(Account::class, [
            'user_id' => $user->id
        ]);

        $otherAccount = $this->create(Account::class);

        $this->addMember($user, $otherAccount);

        $this->assertInstanceOf(Collection::class, $user->allAccounts());
        $this->assertContains($account->id, $user->allAccounts()->pluck('id'));
        $this->assertContains($otherAccount->id, $user->allAccounts()->pluck('id'));
    }

    /** @test */
    public function has_account_role_must_return_null_role_object_when_member_does_not_have_the_role_informed()
    {
        $user = $this->create(User::class);
        $account = $this->create(Account::class);

        $this->addMember($user, $account);

        $role = $user->accountRole($account);

        $this->assertInstanceOf(NullRole::class, $role);
        $this->assertEquals('', $role->key);
        $this->assertEquals([], $role->permissions);
    }

    /** @test */
    public function account_role_must_return_role_object_when_user_is_an_account_member_with_that_same_role()
    {
        $user = $this->create(User::class);
        $account = $this->create(Account::class);

        RBAC::role(new Role('admin', [
            'posts.create',
            'posts.update',
            'posts.delete'
        ]));

        $this->addMember($user, $account, 'admin');

        $role = $user->accountRole($account);

        $this->assertInstanceOf(Role::class, $role);
        $this->assertEquals('admin', $role->key);
        $this->assertEquals([
            'posts.create',
            'posts.update',
            'posts.delete'
        ], $role->permissions);
    }

    /** @test */
    public function account_role_must_return_owner_role_object_when_user_aowns_the_account()
    {
        $user = $this->create(User::class);
        $account = $this->create(Account::class, [
            'user_id' => $user->id
        ]);

        $this->addMember($user, $account, 'admin');

        $role = $user->accountRole($account);

        $this->assertInstanceOf(OwnerRole::class, $role);
        $this->assertEquals('owner', $role->key);
        $this->assertEquals(['*'], $role->permissions);
    }

    /** @test */
    public function has_account_role_must_return_false_when_member_does_not_have_the_role_informed()
    {
        $user = $this->create(User::class);
        $account = $this->create(Account::class);

        RBAC::role(new Role('admin'));

        $this->addMember($user, $account, 'admin');

        $this->assertFalse($user->hasAccountRole($account, 'does_not_have'));
    }

    /** @test */
    public function has_account_role_must_return_true_when_user_is_the_account_owner()
    {
        $user = $this->create(User::class);
        $account = $this->create(Account::class, [
            'user_id' => $user->id
        ]);

        $this->addMember($user, $account);

        $this->assertTrue($user->hasAccountRole($account, 'admin'));
    }

    /** @test */
    public function has_account_role_must_return_true_when_member_has_the_role_informed()
    {
        $user = $this->create(User::class);
        $account = $this->create(Account::class);

        RBAC::role(new Role('admin'));

        $this->addMember($user, $account, 'admin');

        $this->assertTrue($user->hasAccountRole($account, 'admin'));
    }

    /** @test */
    public function has_account_permission_must_return_false_when_member_does_not_have_the_informed_permission()
    {
        $user = $this->create(User::class);
        $account = $this->create(Account::class);

        RBAC::role(new Role('admin', [
            'posts.create',
            'posts.update',
            'posts.delete'
        ]));

        $this->addMember($user, $account, 'admin');

        $this->assertFalse($user->hasAccountPermission($account, 'does_not_have'));
    }

    /** @test */
    public function has_account_permission_must_return_true_when_user_is_the_account_owner()
    {
        $user = $this->create(User::class);
        $account = $this->create(Account::class, [
            'user_id' => $user->id
        ]);

        $this->addMember($user, $account, 'admin');

        $this->assertTrue($user->hasAccountPermission($account, 'does_not_have'));
    }

    /** @test */
    public function has_account_permission_must_return_true_when_member_has_the_informed_permission()
    {
        $user = $this->create(User::class);
        $account = $this->create(Account::class);

        RBAC::role(new Role('admin', [
            'posts.create',
            'posts.update',
            'posts.delete'
        ]));

        $this->addMember($user, $account, 'admin');

        $this->assertTrue($user->hasAccountPermission($account, 'posts.create'));
    }

    /** @test */
    public function has_account_permission_must_return_true_when_member_has_wildcard_permission()
    {
        $user = $this->create(User::class);
        $account = $this->create(Account::class);

        RBAC::role(new Role('admin', ['*']));

        $this->addMember($user, $account, 'admin');

        $this->assertTrue($user->hasAccountPermission($account, 'does_not_have'));
    }

    /** @test */
    public function has_account_permission_must_return_true_when_member_has_wildcard_create_permission()
    {
        $user = $this->create(User::class);
        $account = $this->create(Account::class);

        RBAC::role(new Role('admin', ['*.create']));

        $this->addMember($user, $account, 'admin');

        $this->assertTrue($user->hasAccountPermission($account, 'posts.create'));
    }

    /** @test */
    public function has_account_permission_must_return_true_when_member_has_wildcard_update_permission()
    {
        $user = $this->create(User::class);
        $account = $this->create(Account::class);

        RBAC::role(new Role('admin', ['*.update']));

        $this->addMember($user, $account, 'admin');

        $this->assertTrue($user->hasAccountPermission($account, 'posts.update'));
    }

    /** @test */
    public function account_permissions_must_return_wildcard_when_user_owns_the_account()
    {
        $user = $this->create(User::class);
        $account = $this->create(Account::class, [
            'user_id' => $user->id
        ]);

        $this->assertEquals(['*'], $user->accountPermissions($account));
    }

    protected function addMember($user, $account, $role = null)
    {
        DB::table('memberships')->insert([
            'user_id' => $user->id,
            'account_id' => $account->id,
            'role' => $role
        ]);
    }
}
