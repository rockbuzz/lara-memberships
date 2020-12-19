<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Tests\Stubs\User;
use Faker\Generator as Faker;
use Rockbuzz\LaraMemberships\Models\Account;

$factory->define(Account::class, function (Faker $faker) {
    return [
        'name' => $faker->sentence(2),
        'user_id' => function () {
            return factory(User::class)->create();
        }
    ];
});
