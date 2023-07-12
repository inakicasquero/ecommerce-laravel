<?php

namespace Webkul\Admin\Http\Controllers\Category;

use Illuminate\Support\Facades\Event;
use Webkul\Admin\DataGrids\CategoryDataGrid;
use Webkul\Admin\DataGrids\CategoryProductDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Attribute\Repositories\AttributeRepository;
use Webkul\Category\Http\Requests\CategoryRequest;
use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Core\Repositories\ChannelRepository;

class CategoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected ChannelRepository $channelRepository,
        protected CategoryRepository $categoryRepository,
        protected AttributeRepository $attributeRepository
    ) {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(CategoryDataGrid::class)->toJson();
        }

        return view('admin::catalog.categories.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $categories = $this->categoryRepository->getCategoryTree(null, ['id']);

        $attributes = $this->attributeRepository->findWhere(['is_filterable' => 1]);

        return view('admin::catalog.categories.create', compact('categories', 'attributes'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $categoryRequest)
    {
        Event::dispatch('catalog.category.create.before');

        $category = $this->categoryRepository->create($categoryRequest->all());

        Event::dispatch('catalog.category.create.after', $category);

        session()->flash('success', trans('admin::app.catalog.categories.create-success'));

        return redirect()->route('admin.catalog.categories.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $category = $this->categoryRepository->findOrFail($id);

        $categories = $this->categoryRepository->getCategoryTreeWithoutDescendant($id);

        $attributes = $this->attributeRepository->findWhere(['is_filterable' => 1]);

        return view('admin::catalog.categories.edit', compact('category', 'categories', 'attributes'));
    }

    /**
     * Show the products of specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function products($id)
    {
        if (request()->ajax()) {
            return app(CategoryProductDataGrid::class)->toJson();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryRequest $categoryRequest, $id)
    {
        Event::dispatch('catalog.category.update.before', $id);

        $category = $this->categoryRepository->update($categoryRequest->all(), $id);

        Event::dispatch('catalog.category.update.after', $category);

        session()->flash('success', trans('admin::app.catalog.categories.update-success'));

        return redirect()->route('admin.catalog.categories.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = $this->categoryRepository->findOrFail($id);

        if ($this->isCategoryDeletable($category)) {
            return response()->json(['message' => trans('admin::app.catalog.categories.delete-category-root')], 400);
        }

        try {
            Event::dispatch('catalog.category.delete.before', $id);

            $this->categoryRepository->delete($id);

            Event::dispatch('catalog.category.delete.after', $id);

            return response()->json(['message' => trans('admin::app.catalog.categories.delete-success')]);
        } catch (\Exception $e) {
        }

        return response()->json(['message' => trans('admin::app.catalog.categories.delete-failed')], 500);
    }

    /**
     * Remove the specified resources from database.
     *
     * @return \Illuminate\Http\Response
     */
    public function massDestroy()
    {
        $suppressFlash = true;

        $categoryIds = explode(',', request()->input('indexes'));

        foreach ($categoryIds as $categoryId) {
            $category = $this->categoryRepository->find($categoryId);

            if (isset($category)) {
                if ($this->isCategoryDeletable($category)) {
                    $suppressFlash = false;

                    session()->flash('warning', trans('admin::app.response.delete-category-root', ['name' => 'Category']));
                } else {
                    try {
                        $suppressFlash = true;

                        Event::dispatch('catalog.category.delete.before', $categoryId);

                        $this->categoryRepository->delete($categoryId);

                        Event::dispatch('catalog.category.delete.after', $categoryId);
                    } catch (\Exception $e) {
                        session()->flash('error', trans('admin::app.response.delete-failed', ['name' => 'Category']));
                    }
                }
            }
        }

        if (
            count($categoryIds) != 1
            || $suppressFlash == true
        ) {
            session()->flash('success', trans('admin::app.datagrid.mass-ops.delete-success', ['resource' => 'Category']));
        }

        return redirect()->route('admin.catalog.categories.index');
    }

    /**
     * Mass update Category.
     *
     * @return \Illuminate\Http\Response
     */
    public function massUpdate()
    {
        $data = request()->all();

        if (
            ! isset($data['mass-action-type'])
            || $data['mass-action-type'] != 'update'
        ) {
            return redirect()->back();
        }

        $categoryIds = explode(',', $data['indexes']);

        foreach ($categoryIds as $categoryId) {
            Event::dispatch('catalog.categories.mass-update.before', $categoryId);

            $category = $this->categoryRepository->find($categoryId);

            $category->status = $data['update-options'];
            $category->save();

            Event::dispatch('catalog.categories.mass-update.after', $category);
        }

        session()->flash('success', trans('admin::app.catalog.categories.mass-update-success'));

        return redirect()->route('admin.catalog.categories.index');
    }

    /**
     * Get category product count.
     *
     * @return \Illuminate\Http\Response
     */
    public function categoryProductCount()
    {
        $productCount = 0;

        $indexes = explode(',', request()->input('indexes'));

        foreach ($indexes as $index) {
            $category = $this->categoryRepository->find($index);

            $productCount += $category->products->count();
        }

        return response()->json(['product_count' => $productCount]);
    }

    /**
     * Check whether the current category is deletable or not.
     *
     * This method will fetch all root category ids from the channel. If `id` is present,
     * then it is not deletable.
     *
     * @param  \Webkul\Category\Contracts\Category  $category
     * @return bool
     */
    private function isCategoryDeletable($category)
    {
        static $channelRootCategoryIds;

        if (! $channelRootCategoryIds) {
            $channelRootCategoryIds = $this->channelRepository->pluck('root_category_id');
        }

        return $category->id === 1 || $channelRootCategoryIds->contains($category->id);
    }
}
