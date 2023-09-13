<?php

namespace App\Http\Controllers;

use App\Actions\Unit\SaveUnitAction;
use App\Filters\SearchFilter;
use App\Http\Requests\Unit\StoreUnitRequest;
use App\Http\Requests\Unit\UpdateUnitRequest;
use App\Models\Unit;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = QueryBuilder::for(Unit::class)
            ->allowedFilters(
                AllowedFilter::custom('search', new SearchFilter(), 'name,symbol'),
            )
            ->allowedSorts(['name', 'symbol'])
            ->allowedFields(['name', 'symbol'])
            ->paginate(30);

        return inertia('Unit/Index', [
            'list' => $data,
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
    public function store(StoreUnitRequest $request, SaveUnitAction $action)
    {
        $action->execute(new Unit(), $request->validated());
        return redirect()->route('units.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Unit $unit)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit $unit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUnitRequest $request, Unit $unit, SaveUnitAction $action)
    {
        $action->execute($unit, $request->validated());
        return redirect()->route('units.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit)
    {
        $unit->delete();

        return redirect()->route('units.index');
    }
}
