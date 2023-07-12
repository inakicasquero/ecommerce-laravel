<?php

namespace Webkul\Admin\Http\Controllers\Inventory;

use Illuminate\Support\Facades\Event;
use Webkul\Admin\DataGrids\InventorySourcesDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Inventory\Http\Requests\InventorySourceRequest;
use Webkul\Inventory\Repositories\InventorySourceRepository;

class InventorySourceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected InventorySourceRepository $inventorySourceRepository)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(InventorySourcesDataGrid::class)->toJson();
        }

        return view('admin::settings.inventory_sources.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin::settings.inventory_sources.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(InventorySourceRequest $inventorySourceRequest)
    {
        Event::dispatch('inventory.inventory_source.create.before');

        $inventorySource = $this->inventorySourceRepository->create(array_merge($inventorySourceRequest->all(), [
            'status' => request()->has('status'),
        ]));

        Event::dispatch('inventory.inventory_source.create.after', $inventorySource);

        session()->flash('success', trans('admin::app.settings.inventory_sources.create-success'));

        return redirect()->route('admin.inventory_sources.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $inventorySource = $this->inventorySourceRepository->findOrFail($id);

        return view('admin::settings.inventory_sources.edit', compact('inventorySource'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(InventorySourceRequest $inventorySourceRequest, $id)
    {
        Event::dispatch('inventory.inventory_source.update.before', $id);

        $inventorySource = $this->inventorySourceRepository->update(array_merge($inventorySourceRequest->all(), [
            'status' => request()->has('status'),
        ]), $id);

        Event::dispatch('inventory.inventory_source.update.after', $inventorySource);

        session()->flash('success', trans('admin::app.settings.inventory_sources.update-success'));

        return redirect()->route('admin.inventory_sources.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->inventorySourceRepository->findOrFail($id);

        if ($this->inventorySourceRepository->count() == 1) {
            return response()->json(['message' => trans('admin::app.settings.inventory_sources.last-delete-error')], 400);
        }

        try {
            Event::dispatch('inventory.inventory_source.delete.before', $id);

            $this->inventorySourceRepository->delete($id);

            Event::dispatch('inventory.inventory_source.delete.after', $id);

            return response()->json(['message' => trans('admin::app.settings.inventory_sources.delete-success')]);
        } catch (\Exception $e) {
            report($e);
        }

        return response()->json(['message' => trans('admin::app.response.delete-failed', ['name' => 'Inventory source'])], 500);
    }
}
