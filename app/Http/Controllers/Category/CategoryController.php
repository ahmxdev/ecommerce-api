<?php

namespace App\Http\Controllers\Category;

use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;

class CategoryController
{
    public function index()
    {
        $categories = Category::with('parent')->get();
        return CategoryResource::collection($categories);
    }
    public function store(StoreCategoryRequest $request)
    {
        $category = Category::create($request->validated());
        $category->load('parent');

        return (new CategoryResource($category))->response()->setStatusCode(201);
    }
    public function show(Category $category)
    {
        $category->load('parent');
        return new CategoryResource($category);
    }
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $category->update($request->validated());
        $category->load('parent');
        return new CategoryResource($category);
    }
    public function destroy(Category $category)
    {
        if ($category->children()->exists()) {
            return response()->json([
                'message' => 'delete restricted'
            ], 409);
        }
        $category->delete();
        return response()->noContent();
    }
}
