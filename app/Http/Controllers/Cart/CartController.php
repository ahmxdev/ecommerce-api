<?php

namespace App\Http\Controllers\Cart;

use App\Http\Requests\Cart\DestroyCartItemRequest;
use App\Http\Requests\Cart\StoreCartItemRequest;
use App\Http\Requests\Cart\UpdateCartItemRequest;
use App\Http\Resources\Cart\CartResource;
use App\Models\CartItem;
use Illuminate\Http\Request;


class CartController
{
    public function index(Request $request) // getting cart of current user
    {
        $cart = $request->user()->cart()->with('items.product')->first();
        return new CartResource($cart);
    }
    public function store(StoreCartItemRequest $request)
    {
        $cart = $request->user()->cart()->firstOrCreate();

        $cart->items()->create(
            $request->validated()
        );

        return response()->json(
            ['message' => 'item created'],
            201
        );
    }
    // public function show(string $id) {}
    public function update(UpdateCartItemRequest $request, CartItem $cartItem)
    {
        $cartItem->update($request->validated());
        return response()->json(
            ['message' => 'item updated'],
            200
        );
    }
    public function destroy(DestroyCartItemRequest $request, CartItem $cartItem)
    {
        $cartItem->delete();
        return response()->noContent();
    }
}
