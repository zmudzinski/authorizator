<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;


$factory->define(\Tzm\Authorizator\Model\Authorization::class, function (Faker $faker) {
    return [
        'user_id'           => factory(\App\User::class)->create()->id,
        'class'             => 'test',
        'uuid'              => 'test',
        'sent_via'          => 'test',
        'verification_code' => 1234,
        'expires_at'        => now()->addMinutes(5),
    ];
});
