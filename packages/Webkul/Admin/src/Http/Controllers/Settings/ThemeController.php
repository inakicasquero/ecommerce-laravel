<?php

namespace Webkul\Admin\Http\Controllers\Settings;

use Illuminate\Support\Facades\Event;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Shop\Repositories\ThemeCustomizationRepository;
use Webkul\Admin\DataGrids\Theme\ThemeDatagrid;

class ThemeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(public ThemeCustomizationRepository $themeCustomizationRepository)
    {
    }

    /**
     * Display a listing resource for the available tax rates.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(ThemeDatagrid::class)->toJson();
        }

        return view('admin::settings.themes.index');
    }

    /**
     * Store a newly created resource in storage.
     * 
     * @return \Illuminate\Http\Resources\Json\JsonResource|string
     */
    public function store()
    {
        if (request()->has('id')) {
            $theme = $this->themeCustomizationRepository->find(request()->input('id'));

            return $this->themeCustomizationRepository->uploadImage(request()->all(), $theme);
        }

        $this->validate(request(), [
            'name'       => 'required',
            'sort_order' => 'required|numeric',
            'type'       => 'in:product_carousel,category_carousel,static_content,image_carousel,footer_links'
        ]);

        Event::dispatch('theme_customization.create.before');

        $theme = $this->themeCustomizationRepository->create([
            'name'       => request()->input('name'),
            'sort_order' => request()->input('sort_order'),
            'type'       => request()->input('type'),
        ]);

        Event::dispatch('theme_customization.create.after', $theme);

        return new JsonResource([
            'redirect_url' => route('admin.theme.edit', $theme->id),
        ]);
    }

    /**
     * Edit the theme
     *
     * @param integer $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $theme = $this->themeCustomizationRepository->find($id);

        return view('admin::settings.themes.edit', compact('theme'));
    }

    /**
     * Update the specified resource
     *
     * @param integer $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id)
    {
        $data = request()->only(['options', 'type', 'name', 'sort_order']);

        if ($data['type'] == 'static_content') {
            $data['options']['html'] = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $data['options']['html']); 
            $data['options']['css'] = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $data['options']['css']); 
        }

        $data['status'] = request()->input('status') == 'on';

        if ($data['type'] == 'image_carousel') {
            unset($data['options']);
        }

        Event::dispatch('theme_customization.update.before', $id);

        $theme = $this->themeCustomizationRepository->update($data, $id);

        if ($data['type'] == 'image_carousel') {
            $this->themeCustomizationRepository->uploadImage(
                request()->all('options'), 
                $theme,
                request()->input('deleted_sliders', [])
            );
        }

        Event::dispatch('theme_customization.update.after', $theme);

        session()->flash('success', trans('admin::app.settings.themes.update-success'));

        return redirect()->route('admin.theme.index');
    }

    /**
     * Delete a specified theme
     *
     * @return void
     */
    public function destroy($id)
    {
        Event::dispatch('theme_customization.delete.before', $id);

        $theme = $this->themeCustomizationRepository->find($id);

        $theme?->delete();

        Storage::deleteDirectory('theme/'. $theme->id);

        Event::dispatch('theme_customization.delete.after', $id);

        return response()->json([
            'data' => [
                'message' => trans('admin::app.settings.themes.delete-success'),
            ]
        ], 200);
    }
}
