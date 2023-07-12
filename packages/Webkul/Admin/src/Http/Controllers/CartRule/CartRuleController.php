<?php

namespace Webkul\Admin\Http\Controllers\CartRule;

use Exception;
use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Webkul\Admin\DataGrids\CartRuleDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\CartRule\Http\Requests\CartRuleRequest;
use Webkul\CartRule\Repositories\CartRuleRepository;

class CartRuleController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected CartRuleRepository $cartRuleRepository)
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
            return app(CartRuleDataGrid::class)->toJson();
        }

        return view('admin::marketing.promotions.cart-rules.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin::marketing.promotions.cart-rules.create');
    }

    /**
     * Copy a given Cart Rule id. Always make the copy is inactive so the
     * user is able to configure it before setting it live.
     */
    public function copy(int $cartRuleId): View
    {
        $cartRule = $this->cartRuleRepository
            ->with([
                'channels',
                'customer_groups',
            ])
            ->findOrFail($cartRuleId);

        $copiedCartRule = $cartRule
            ->replicate()
            ->fill([
                'status' => 0,
                'name'   => trans('admin::app.copy-of', ['value' => $cartRule->name]),
            ]);

        $copiedCartRule->save();

        foreach ($copiedCartRule->channels as $channel) {
            $copiedCartRule->channels()->save($channel);
        }

        foreach ($copiedCartRule->customer_groups as $group) {
            $copiedCartRule->customer_groups()->save($group);
        }

        return view('admin::marketing.promotions.cart-rules.edit', [
            'cartRule' => $copiedCartRule,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(CartRuleRequest $cartRuleRequest)
    {
        try {
            Event::dispatch('promotions.cart_rule.create.before');

            $cartRule = $this->cartRuleRepository->create($cartRuleRequest->all());

            Event::dispatch('promotions.cart_rule.create.after', $cartRule);

            session()->flash('success', trans('admin::app.promotions.cart-rules.create-success'));

            return redirect()->route('admin.cart_rules.index');
        } catch (ValidationException $e) {
            if ($firstError = collect($e->errors())->first()) {
                session()->flash('error', $firstError[0]);
            }
        }

        return redirect()->back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $cartRule = $this->cartRuleRepository->findOrFail($id);

        return view('admin::marketing.promotions.cart-rules.edit', compact('cartRule'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CartRuleRequest $cartRuleRequest, $id)
    {
        try {
            $cartRule = $this->cartRuleRepository->findOrFail($id);

            if ($cartRule->coupon_type) {
                if ($cartRule->cart_rule_coupon) {
                    $this->validate(request(), [
                        'coupon_code' => 'required_if:use_auto_generation,==,0|unique:cart_rule_coupons,code,' . $cartRule->cart_rule_coupon->id,
                    ]);
                } else {
                    $this->validate(request(), [
                        'coupon_code' => 'required_if:use_auto_generation,==,0|unique:cart_rule_coupons,code',
                    ]);
                }
            }

            Event::dispatch('promotions.cart_rule.update.before', $id);

            $cartRule = $this->cartRuleRepository->update($cartRuleRequest->all(), $id);

            Event::dispatch('promotions.cart_rule.update.after', $cartRule);

            session()->flash('success', trans('admin::app.promotions.cart-rules.update-success'));

            return redirect()->route('admin.cart_rules.index');
        } catch (ValidationException $e) {
            if ($firstError = collect($e->errors())->first()) {
                session()->flash('error', $firstError[0]);
            }
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->cartRuleRepository->findOrFail($id);

        try {
            Event::dispatch('promotions.cart_rule.delete.before', $id);

            $this->cartRuleRepository->delete($id);

            Event::dispatch('promotions.cart_rule.delete.after', $id);

            return response()->json(['message' => trans('admin::app.promotions.cart-rules.delete-success')]);
        } catch (Exception $e) {
        }

        return response()->json(['message' => trans('admin::app.promotions.cart-rules.delete-failed')], 400);
    }
}
