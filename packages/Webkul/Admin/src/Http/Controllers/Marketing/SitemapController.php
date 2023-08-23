<?php

namespace Webkul\Admin\Http\Controllers\Marketing;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Sitemap\Repositories\SitemapRepository;
use Webkul\Admin\DataGrids\SitemapDataGrid;

class SitemapController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(public SitemapRepository $sitemapRepository)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(SitemapDataGrid::class)->toJson();
        }

        return view('admin::marketing.sitemaps.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin::marketing.sitemaps.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'file_name' => 'required',
            'path'      => 'required',
        ]);

        Event::dispatch('marketing.sitemaps.create.before');

        $sitemap = $this->sitemapRepository->create(request()->only([
            'file_name',
            'path'
        ]));

        Event::dispatch('marketing.sitemaps.create.after', $sitemap);

        session()->flash('success', trans('admin::app.marketing.sitemaps.create-success'));

        return redirect()->route('admin.sitemaps.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $sitemap = $this->sitemapRepository->findOrFail($id);

        return view('admin::marketing.sitemaps.edit', compact('sitemap'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $this->validate(request(), [
            'file_name' => 'required',
            'path'      => 'required',
        ]);

        Event::dispatch('marketing.sitemaps.update.before', $id);

        $sitemap = $this->sitemapRepository->update(request()->only([
            'file_name',
            'path'
        ]), $id);

        Event::dispatch('marketing.sitemaps.update.after', $sitemap);

        session()->flash('success', trans('admin::app.marketing.sitemaps.update-success'));

        return redirect()->route('admin.sitemaps.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $sitemap = $this->sitemapRepository->findOrFail($id);

        Storage::delete($sitemap->path . '/' . $sitemap->file_name);

        try {
            Event::dispatch('marketing.sitemaps.delete.before', $id);

            $this->sitemapRepository->delete($id);

            Event::dispatch('marketing.sitemaps.delete.after', $id);

            return response()->json(['message' => trans('admin::app.marketing.sitemaps.delete-success')]);
        } catch (\Exception $e) {
        }

        return response()->json(['message' => trans('admin::app.response.delete-failed', ['name' => 'Sitemap'])], 500);
    }
}
