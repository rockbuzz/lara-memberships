<?php

namespace Tests;

use Tests\Stubs\User;
use Illuminate\Support\Facades\DB;
use Rockbuzz\LaraMemberships\Models\Account;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AccountTest extends TestCase
{
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
