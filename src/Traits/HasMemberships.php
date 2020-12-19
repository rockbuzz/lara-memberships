<?php

namespace Rockbuzz\LaraMemberships\Traits;

use Rockbuzz\LaraMemberships\RBAC;
use Rockbuzz\LaraMemberships\Models\Account;
use Rockbuzz\LaraMemberships\Roles\OwnerRole;

trait HasMemberships
{
    public function ownedAccounts()
    {
        return $this->hasMany(Account::class);
    }

    public function allAccounts()
    {
        return $this->ownedAccounts->merge($this->accounts);
    }

    public function accounts()
    {
        return $this->belongsToMany(Account::class, 'memberships')
                    ->withPivot('role')
                    ->withTimestamps()
                    ->as('membership');
    }

    public function accountRole(Account $account)
    {
        if ($this->ownsAccount($account)) {
            return new OwnerRole();
        }

        return RBAC::findRole($account->userById($this->id)->membership->role);
    }

    public function hasAccountRole(Account $account, string $role)
    {
        if ($this->ownsAccount($account)) {
            return true;
        }

        return RBAC::findRole($account->userById($this->id)->membership->role)->key === $role;
    }

    public function ownsAccount(Account $account)
    {
        return $this->id === $account->user_id;
    }
}
