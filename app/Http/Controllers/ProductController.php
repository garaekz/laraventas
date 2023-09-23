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
use Illuminate\Support\Facades\Log;
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
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products');
        }
        try {
            DB::beginTransaction();

            $product = $action->execute(new Product(), $data);

            DB::commit();

            return redirect()->route('products.index')
                ->withSuccess("Product {$product->name} created successfully.");
        } catch (\Throwable $th) {
            DB::rollBack();

            if (isset($data['image'])) {
                Storage::delete($data['image']);
            }

            Log::error($th->getMessage());

            return back()->withError($th->getMessage());
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
    public function update(UpdateProductRequest $request, Product $product, SaveProductAction $action)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::delete($product->image);
            }

            $data['image'] = $request->file('image')->store('products');
        }

        try {
            DB::beginTransaction();

            $product = $action->execute($product, $data);

            DB::commit();

            return redirect()->route('products.index')
                ->withSuccess("Product {$product->name} updated successfully.");
        } catch (\Throwable $th) {
            DB::rollBack();

            if (isset($data['image'])) {
                Storage::delete($data['image']);
            }

            Log::error($th->getMessage());

            return back()->withError($th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        //
    }
}
