<?php

namespace Rockbuzz\LaraMemberships\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    protected $guarded = [];

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
}
