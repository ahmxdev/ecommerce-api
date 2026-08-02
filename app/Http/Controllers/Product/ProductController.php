<?php

namespace App\Http\Controllers\Product;

use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\Product\ProductIndexResource;
use App\Http\Resources\Product\ProductShowResource;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController
{
    public function index()
    {
        $products = Product::select(['id', 'name', 'price', 'stock', 'image_path'])->paginate();
        return ProductIndexResource::collection($products);
    }
    public function store(StoreProductRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request->safe()->except('categories', 'image');
            $categories = $request->validated('categories');

            if ($request->hasFile('image')) {
                $data['image_path'] = $request->file('image')
                    ->store('products', 'public');
            }

            $product = Product::create($data);
            $product->categories()->sync($categories);

            $product->load(['brand', 'categories']);
            return new ProductShowResource($product);
        });
    }
    public function show(Product $product)
    {
        $product->load([
            'brand:id,name',
            'categories:id,name',
        ]);
        return new ProductShowResource($product);
    }
    public function update(UpdateProductRequest $request, Product $product)
    {
        return DB::transaction(function () use ($request, $product) {
            $data = $request->safe()->except('categories', 'image');
            $categories = $request->validated('categories');
            if ($request->hasFile('image')) {
                $data['image_path'] = $request->file('image')
                    ->store('products', 'public');
                Storage::disk('public')->delete($product->image_path);
            }

            $product->update($data);
            $product->categories()->sync($categories);

            $product->load(['brand', 'categories']);
            return new ProductShowResource($product);
        });
    }
    public function destroy(Product $product)
    {
        Storage::disk('public')->delete($product->image_path);
        $product->delete();
        return response()->noContent();
    }
}
