<?php

namespace Tests;

use DomainException;
use Tests\Stubs\User;
use Rockbuzz\LaraMemberships\Role;
use Illuminate\Support\Facades\{DB, Config};
use Rockbuzz\LaraMemberships\Models\Account;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AccountTest extends TestCase
{
    /** @test */
    public function an_account_have_owner()
    {
        $user = $this->create(User::class);
        $account = $this->create(Account::class, [
            'user_id' => $user->id
        ]);

        $this->assertEquals($user->id, $account->owner->id);
    }

    /** @test */
    public function an_account_can_add_member()
    {
        $user = $this->create(User::class);
        $account = $this->create(Account::class);

        $this->assertInstanceOf(Account::class, $account->addMember($user));
        $this->assertDatabaseHas('memberships', [
            'user_id' => $user->id,
            'account_id' => $account->id,
            'role' => null
        ]);
    }

    /** @test */
    public function an_account_can_add_member_with_role()
    {
        $user = $this->create(User::class);
        $account = $this->create(Account::class);
        $role = new Role('admin', ['*']);

        $this->assertInstanceOf(Account::class, $account->addMember($user, $role));
        $this->assertDatabaseHas('memberships', [
            'user_id' => $user->id,
            'account_id' => $account->id,
            'role' => 'admin'
        ]);
    }

    /** @test */
    public function add_member_must_return_exception_when_member_not_a_user_model()
    {
        $user = $this->create(User::class);
        $account = $this->create(Account::class);

        Config::set('memberships.models.user', 'Other\Model');

        $this->expectException(DomainException::class);

        $userModel = config('memberships.models.user');

        $this->expectExceptionMessage("User argument must be a {$userModel} model");

        $account->addMember($user);
    }

    /** @test */
    public function an_account_can_have_members()
    {
        $user = $this->create(User::class);
        $account = $this->create(Account::class);

        DB::table('memberships')->insert([
            'user_id' => $user->id,
            'account_id' => $account->id
        ]);

        $this->assertInstanceOf(BelongsToMany::class, $account->members());
        $this->assertContains($user->id, $account->members->pluck('id'));
    }
}
