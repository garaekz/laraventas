<?php

namespace App\Http\Controllers;

use App\Actions\Product\SaveProductAction;
use App\Filters\SearchFilter;
use App\Models\Product;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Brand;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = QueryBuilder::for(Product::class)
            ->allowedFilters(
                AllowedFilter::custom('search', new SearchFilter(), 'name,code'),
            )
            ->allowedSorts(['name', 'code', 'price', 'stock', 'min_stock', 'status'])
            ->with('brand:id,name', 'unit:id,name')
            ->paginate(30);

        return inertia('Product/Index', [
            'list' => $data,
            'units' => Unit::select('id', 'name')->get(),
            'brands' => Brand::select('id', 'name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request, SaveProductAction $action)
    {
        try {
            $data = $request->validated();

            DB::beginTransaction();

            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('products');
            }

            $product = $action->execute(new Product(), $data);

            DB::commit();

            return redirect()->route('products.index')
                ->with('success', "Product {$product->name} created successfully.");
        } catch (\Throwable $th) {
            DB::rollBack();

            // Delete image if exist
            if (isset($data['image'])) {
                Storage::delete($data['image']);
            }

            return back()->with('error', $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
