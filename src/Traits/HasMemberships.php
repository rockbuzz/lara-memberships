<?php

declare(strict_types=1);

namespace Rockbuzz\LaraMemberships\Traits;

use Illuminate\Support\Str;
use Rockbuzz\LaraMemberships\{RBAC, Role};
use Rockbuzz\LaraMemberships\Models\Account;
use Illuminate\Database\Eloquent\Collection;
use Rockbuzz\LaraMemberships\Roles\OwnerRole;
use Illuminate\Database\Eloquent\Relations\{HasMany, BelongsToMany};

trait HasMemberships
{
    public function ownedAccounts(): HasMany
    {
        return $this->hasMany(Account::class);
    }

    public function accounts(): BelongsToMany
    {
        return $this->belongsToMany(Account::class, 'memberships')
                    ->withPivot('role')
                    ->withTimestamps()
                    ->as('membership');
    }

    public function allAccounts(): Collection
    {
        return $this->ownedAccounts->merge($this->accounts);
    }

    public function accountRole(Account $account): Role
    {
        if ($this->ownsAccount($account)) {
            return new OwnerRole();
        }

        return RBAC::findRole($account->findMemberById($this->id)->membership->role);
    }

    public function hasAccountRole(Account $account, string $role): bool
    {
        if ($this->ownsAccount($account)) {
            return true;
        }

        return RBAC::findRole($account->findMemberById($this->id)->membership->role)->key === $role;
    }

    public function accountPermissions($account): array
    {
        if ($this->ownsAccount($account)) {
            return ['*'];
        }

        return $this->accountRole($account)->permissions;
    }

    public function hasAccountPermission(Account $account, string $permission): bool
    {
        if ($this->ownsAccount($account)) {
            return true;
        }

        $permissions = $this->accountPermissions($account);

        return in_array($permission, $permissions) ||
            in_array('*', $permissions) ||
            $this->hasWildcard($permission, $permissions, 'create') ||
            $this->hasWildcard($permission, $permissions, 'update');
    }

    public function ownsAccount(Account $account): bool
    {
        return $this->id === $account->user_id;
    }

    protected function hasWildcard(string $permission, array $permissions, $action): bool
    {
        return Str::endsWith($permission, ".{$action}") && in_array("*.{$action}", $permissions);
    }
}
