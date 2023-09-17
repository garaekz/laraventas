<?php

namespace App\Http\Controllers;

use App\Actions\Brand\SaveBrandAction;
use App\Filters\SearchFilter;
use App\Models\Brand;
use App\Http\Requests\StoreBrandRequest;
use App\Http\Requests\UpdateBrandRequest;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = QueryBuilder::for(Brand::class)
            ->allowedFilters(
                AllowedFilter::custom('search', new SearchFilter(), 'name'),
            )
            ->allowedSorts(['id, name, created_at'])
            ->allowedFields(['name'])
            ->paginate(30);

        return inertia('Brand/Index', [
            'list' => $data,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBrandRequest $request, SaveBrandAction $action)
    {
        $action->execute(new Brand(), $request->validated());

        return redirect()->route('brands.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBrandRequest $request, Brand $brand, SaveBrandAction $action)
    {
        $action->execute($brand, $request->validated());

        return redirect()->route('brands.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Brand $brand)
    {
        $brand->delete();

        return redirect()->route('brands.index');
    }
}
