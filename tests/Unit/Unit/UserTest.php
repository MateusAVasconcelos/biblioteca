<?php

use App\Models\User;

it('returns initials from a two-word name', function () {
    $user = new User(['name' => 'John Doe']);

    expect($user->initials())->toBe('JD');
});

it('returns initials from a one-word name', function () {
    $user = new User(['name' => 'John']);

    expect($user->initials())->toBe('J');
});

it('returns only the first two initials from a multi-word name', function () {
    $user = new User(['name' => 'John Michael Doe']);

    expect($user->initials())->toBe('JM');
});
