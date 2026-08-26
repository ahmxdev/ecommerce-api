<?php

namespace App\Policies;

use App\Models\CartItem;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CartItemPolicy
{
    public function update(User $user, CartItem $cartItem): Response
    {
        return $user->id === $cartItem->cart->user_id
            ? Response::allow()
            : Response::denyAsNotFound();
    }

    public function delete(User $user, CartItem $cartItem): Response
    {
        return $user->id === $cartItem->cart->user_id
            ? Response::allow()
            : Response::denyAsNotFound();
    }
}
