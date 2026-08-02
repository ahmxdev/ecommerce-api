<?php

namespace App\Http\Controllers\Address;

use App\Http\Requests\Address\StoreAddressRequest;
use App\Http\Requests\Address\UpdateAddressRequest;
use App\Http\Resources\Address\AddressResource;
use App\Models\Address;
use Illuminate\Http\Request;

class AddressController
{
    public function index(Request $request)
    {
        $addresses = $request->user()->addresses()->get();
        return AddressResource::collection($addresses);
    }
    public function store(StoreAddressRequest $request)
    {
        $address = $request->user()->addresses()->create($request->validated());

        return new AddressResource($address);
    }
    // public function show(string $id) {}
    public function update(UpdateAddressRequest $request, Address $address)
    {
        $address->update($request->validated());
        return new AddressResource($address);
    }
    public function destroy(Address $address)
    {
        $address->delete();
        return response()->noContent();
    }
}
