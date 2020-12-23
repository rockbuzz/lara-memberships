<?php

namespace Rockbuzz\LaraMemberships\Models;

use Illuminate\Support\Facades\DB;
use Rockbuzz\LaraMemberships\Role;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $fillable = [
        'name',
        'user_id'
    ];

    public function users()
    {
        return $this->belongsToMany(config('memberships.models.user'), 'memberships')
                    ->withPivot('role')
                    ->withTimestamps()
                    ->as('membership');
    }

    public function userById(int $id)
    {
        return $this->users->where('id', $id)->first();
    }

    public function addMember(Model $user, Role $role = null)
    {
        DB::table('memberships')->insert([
            'user_id' => $user->id,
            'account_id' => $this->id,
            'role' => $role ? $role->key : null
        ]);

        return $this;
    }
}
