<?php

namespace App\Http\Controllers\Brands;

use App\Http\Requests\Brands\StoreBrandRequest;
use App\Http\Requests\Brands\UpdateBrandRequest;
use App\Http\Resources\Brands\BrandResource;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController
{
    public function index()
    {
        $brands = Brand::all();
        return BrandResource::collection($brands);
    }
    public function store(StoreBrandRequest $request)
    {
        $brand = Brand::create($request->validated());
        return (new BrandResource($brand))->response()->setStatusCode(201);
    }
    public function show(Brand $brand)
    {
        return new BrandResource($brand);
    }
    public function update(UpdateBrandRequest $request, Brand $brand)
    {
        $brand->update($request->validated());
        return new BrandResource($brand);
    }
    public function destroy(Brand $brand)
    {
        $brand->delete();
        return response()->noContent();
    }
}
