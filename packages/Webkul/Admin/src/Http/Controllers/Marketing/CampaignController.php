<?php

namespace Webkul\Admin\Http\Controllers\Marketing;

use Illuminate\Support\Facades\Event;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Marketing\Repositories\CampaignRepository;
use Webkul\Marketing\Repositories\TemplateRepository;
use Webkul\Admin\DataGrids\Marketing\CampaignDataGrid;

class CampaignController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected CampaignRepository $campaignRepository,
        protected TemplateRepository $templateRepository,
    )
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
            return app(CampaignDataGrid::class)->toJson();
        }

        return view('admin::marketing.email-marketing.campaigns.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $templates = $this->templateRepository->findByField('status', 'active');

        return view('admin::marketing.email-marketing.campaigns.create', compact('templates'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'name'                  => 'required',
            'subject'               => 'required',
            'marketing_template_id' => 'required',
            'marketing_event_id'    => 'required_if:schedule_type,event',
        ]);

        Event::dispatch('marketing.campaigns.create.before');

        request()['status'] = request()->input('status') ? request()->input('status') : 0;

        $campaign = $this->campaignRepository->create( request()->only([
            'name',
            'subject',
            'marketing_event_id',
            'marketing_template_id',
            'status',
            'channel_id',
            'customer_group_id',
        ]));

        Event::dispatch('marketing.campaigns.create.after', $campaign);

        session()->flash('success', trans('admin::app.marketing.email-marketing.campaigns.create-success'));

        return redirect()->route('admin.marketing.promotions.campaigns.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $campaign = $this->campaignRepository->findOrFail($id);

        $templates = $this->templateRepository->findByField('status', 'active');

        return view('admin::marketing.email-marketing.campaigns.edit', compact('campaign', 'templates'));
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
            'name'                  => 'required',
            'subject'               => 'required',
            'marketing_template_id' => 'required',
            'marketing_event_id'    => 'required_if:schedule_type,event',
        ]);

        Event::dispatch('marketing.campaigns.update.before', $id);

        request()['status'] = request()->input('status') ? request()->input('status') : 0;

        $campaign = $this->campaignRepository->update(request()->only([
            'name',
            'subject',
            'marketing_event_id',
            'marketing_template_id',
            'status',
            'channel_id',
            'customer_group_id'
        ]), $id);

        Event::dispatch('marketing.campaigns.update.after', $campaign);

        session()->flash('success', trans('admin::app.marketing.email-marketing.campaigns.update-success'));

        return redirect()->route('admin.marketing.promotions.campaigns.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->campaignRepository->findOrFail($id);

        try {
            Event::dispatch('marketing.campaigns.delete.before', $id);

            $this->campaignRepository->delete($id);

            Event::dispatch('marketing.campaigns.delete.after', $id);

            return response()->json(['message' => trans('admin::app.marketing.email-marketing.campaigns.delete-success')]);
        } catch (\Exception $e) {
        }

        return response()->json(['message' => trans('admin::app.response.delete-failed', ['name' => 'Email Campaign'])], 500);
    }
}
