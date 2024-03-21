<?php

namespace Webkul\Admin\Http\Controllers\Customers\Customer;

use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Admin\Http\Resources\CartItemResource;
use Webkul\Checkout\Facades\Cart;
use Webkul\Checkout\Repositories\CartItemRepository;

class CartController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(protected CartItemRepository $cartItemRepository)
    {
    }

    /**
     * Returns the compare items of the customer.
     */
    public function items(int $id): JsonResource
    {
        $cartItems = $this->cartItemRepository
            ->with('product')
            ->select('cart_items.*')
            ->leftJoin('cart', 'cart_items.cart_id', 'cart.id')
            ->whereNull('cart_items.parent_id')
            ->where('cart.customer_id', $id)
            ->where('cart.is_active', 1)
            ->get();

        return CartItemResource::collection($cartItems);
    }

    /**
     * Removes the item from the cart if it exists.
     */
    public function destroy(int $id): JsonResource
    {
        $this->validate(request(), [
            'item_id' => 'required|exists:cart_items,id',
        ]);

        $cartItem = $this->cartItemRepository->findOrFail(request()->input('item_id'));

        Cart::setCart($cartItem->cart);

        Cart::removeItem($cartItem->id);

        Cart::collectTotals();

        return new JsonResource([
            'message' => trans('admin::app.customers.customers.view.cart.delete-success'),
        ]);
    }
}
