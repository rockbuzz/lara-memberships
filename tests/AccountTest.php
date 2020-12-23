<?php

namespace Tests;

use Tests\Stubs\User;
use Illuminate\Support\Facades\DB;
use Rockbuzz\LaraMemberships\Models\Account;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Rockbuzz\LaraMemberships\Role;

class AccountTest extends TestCase
{
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
    public function an_account_can_have_members()
    {
        $user = $this->create(User::class);
        $account = $this->create(Account::class);

        DB::table('memberships')->insert([
            'user_id' => $user->id,
            'account_id' => $account->id
        ]);

        $this->assertInstanceOf(BelongsToMany::class, $account->users());
        $this->assertContains($user->id, $account->users->pluck('id'));
    }
}
