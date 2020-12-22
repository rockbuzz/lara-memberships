<?php

namespace Rockbuzz\LaraMemberships\Traits;

use Illuminate\Support\Str;
use Rockbuzz\LaraMemberships\RBAC;
use Rockbuzz\LaraMemberships\Models\Account;
use Rockbuzz\LaraMemberships\Roles\OwnerRole;

trait HasMemberships
{
    public function ownedAccounts()
    {
        return $this->hasMany(Account::class);
    }

    public function accounts()
    {
        return $this->belongsToMany(Account::class, 'memberships')
                    ->withPivot('role')
                    ->withTimestamps()
                    ->as('membership');
    }

    public function allAccounts()
    {
        return $this->ownedAccounts->merge($this->accounts);
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

    public function hasAccountPermission(Account $account, string $permission)
    {
        if ($this->ownsAccount($account)) {
            return true;
        }

        $permissions = $this->accountPermissions($account);

        return in_array($permission, $permissions) || in_array('*', $permissions);
    }

    public function accountPermissions($account)
    {
        if ($this->ownsAccount($account)) {
            return ['*'];
        }

        return $this->accountRole($account)->permissions;
    }

    public function ownsAccount(Account $account)
    {
        return $this->id === $account->user_id;
    }
}
