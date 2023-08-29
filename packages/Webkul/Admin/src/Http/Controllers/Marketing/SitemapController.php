<?php

namespace Webkul\Admin\Http\Controllers\Marketing;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Sitemap\Repositories\SitemapRepository;
use Webkul\Admin\DataGrids\Marketing\SitemapDataGrid;

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
     * Store a newly created resource in storage.
     *
     * @return JsonResource
     */
    public function store(): JsonResource
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

        return new JsonResource([
            'message' => trans('admin::app.marketing.sitemaps.index.create.success'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return JsonResource
     */
    public function update(): JsonResource
    {
        $id = request()->id;

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

        return new JsonResource([
            'message' => trans('admin::app.marketing.sitemaps.index.edit.success'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResource
     */
    public function destroy($id): JsonResource
    {
        $sitemap = $this->sitemapRepository->findOrFail($id);

        Storage::delete($sitemap->path . '/' . $sitemap->file_name);

        try {
            Event::dispatch('marketing.sitemaps.delete.before', $id);

            $this->sitemapRepository->delete($id);

            Event::dispatch('marketing.sitemaps.delete.after', $id);

            return new JsonResource([
                'message' => trans('admin::app.marketing.sitemaps.index.edit.delete-success'),
            ]);
        } catch (\Exception $e) {
        }

        return new JsonResource([
            'message' => trans('admin::app.marketing.sitemaps.delete-failed', ['name' => 'admin::app.marketing.sitemaps.index.sitemap']),
        ], 500);
    }
}
