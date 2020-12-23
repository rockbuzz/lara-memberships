<?php

namespace Rockbuzz\LaraMemberships\Models;

use Illuminate\Support\Facades\DB;
use Rockbuzz\LaraMemberships\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Account extends Model
{
    protected $fillable = [
        'name',
        'user_id'
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(config('memberships.models.user'), 'user_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(config('memberships.models.user'), 'memberships')
                    ->withPivot('role')
                    ->withTimestamps()
                    ->as('membership');
    }

    public function findMemberById(int $id)
    {
        return $this->members->where('id', $id)->first();
    }

    public function addMember(Model $user, Role $role = null): self
    {
        DB::table('memberships')->insert([
            'user_id' => $user->id,
            'account_id' => $this->id,
            'role' => $role ? $role->key : null
        ]);

        return $this;
    }
}
