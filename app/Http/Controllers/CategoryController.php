<?php

namespace App\Http\Controllers;

use App\Actions\Category\SaveCategoryAction;
use App\Filters\SearchFilter;
use App\Models\Category;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = QueryBuilder::for(Category::class)
            ->allowedFilters(
                AllowedFilter::custom('search', new SearchFilter(), 'name'),
            )
            ->allowedSorts(['id, name, created_at'])
            ->allowedFields(['name'])
            ->paginate(30);

        return inertia('Category/Index', [
            'list' => $data,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request, SaveCategoryAction $action)
    {
        $action->execute(new Category(), $request->validated());

        return redirect()->route('categories.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category, SaveCategoryAction $action)
    {
        $action->execute($category, $request->validated());

        return redirect()->route('categories.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->route('categories.index');
    }
}
