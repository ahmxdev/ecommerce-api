<?php

namespace App\Http\Controllers\Product;

use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\Product\ProductIndexResource;
use App\Http\Resources\Product\ProductShowResource;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductController
{
    public function index()
    {
        $products = Product::with('primaryImage:product_id,image_path')->select(['id', 'name', 'price', 'stock'])->paginate();
        return ProductIndexResource::collection($products);
    }
    public function store(StoreProductRequest $request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request->safe()->except('categories');
            $categories = $request->validated('categories');

            $product = Product::create($data);
            $product->categories()->sync($categories);

            $product->load(['brand', 'categories', /* IMAGES */]);
            return new ProductShowResource($product);
        });
    }
    public function show(Product $product)
    {
        $product->load([
            'brand:id,name',
            'categories:id,name',
            /*'images:id,product_id,image_path,is_primary'*/ // IMAGES
        ]);
        return new ProductShowResource($product);
    }
    public function update(UpdateProductRequest $request, Product $product)
    {
        return DB::transaction(function () use ($request, $product) {
            $data = $request->safe()->except('categories');
            $categories = $request->validated('categories');

            $product->update($data);
            $product->categories()->sync($categories);

            $product->load(['brand', 'categories', /* IMAGES */]);
            return new ProductShowResource($product);
        });
    }
    public function destroy(Product $product)
    {
        $product->delete();
        return response()->noContent();
    }
}
