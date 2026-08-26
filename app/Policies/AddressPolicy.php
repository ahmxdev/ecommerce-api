<?php

namespace App\Policies;

use App\Models\Address;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AddressPolicy
{
    public function update(User $user, Address $address): Response
    {
        return $user->id === $address->user_id
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    public function delete(User $user, Address $address): Response
    {
        return $user->id === $address->user_id
            ? Response::allow()
            : Response::denyAsNotFound();
    }
}
