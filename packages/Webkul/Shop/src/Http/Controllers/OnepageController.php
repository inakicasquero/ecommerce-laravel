<?php

namespace Webkul\Shop\Http\Controllers;

use Illuminate\Support\Facades\Event;
use OpenAI\Laravel\Facades\OpenAI;
use Webkul\Checkout\Facades\Cart;

class OnepageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        Event::dispatch('checkout.load.index');

        /**
         * If guest checkout is not allowed then redirect back to the cart page
         */
        if (
            ! auth()->guard('customer')->check()
            && ! core()->getConfigData('catalog.products.guest_checkout.allow_guest_checkout')
        ) {
            return redirect()->route('shop.customer.session.index');
        }

        /**
         * If user is suspended then redirect back to the cart page
         */
        if (auth()->guard('customer')->user()?->is_suspended) {
            session()->flash('warning', trans('shop::app.checkout.cart.suspended-account-message'));

            return redirect()->route('shop.checkout.cart.index');
        }

        /**
         * If cart has errors then redirect back to the cart page
         */
        if (Cart::hasError()) {
            return redirect()->route('shop.checkout.cart.index');
        }

        $cart = Cart::getCart();

        /**
         * If cart is has downloadable items and customer is not logged in
         * then redirect back to the cart page
         */
        if (
            ! auth()->guard('customer')->check()
            && (
                $cart->hasDownloadableItems()
                || ! $cart->hasGuestCheckoutItems()
            )
        ) {
            return redirect()->route('shop.customer.session.index');
        }

        /**
         * If cart minimum order amount is not satisfied then redirect back to the cart page
         */
        $minimumOrderAmount = (float) core()->getConfigData('sales.order_settings.minimum_order.minimum_order_amount') ?: 0;

        if (! $cart->checkMinimumOrder()) {
            session()->flash('warning', trans('shop::app.checkout.cart.minimum-order-message', [
                'amount' => core()->currency($minimumOrderAmount),
            ]));

            return redirect()->back();
        }

        return view('shop::checkout.onepage.index');
    }

    /**
     * Order success page.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function success()
    {
        if (! $order = session('order')) {
            return redirect()->route('shop.checkout.cart.index');
        }

        if (
            core()->getConfigData('general.magic_ai.settings.enabled')
            && core()->getConfigData('general.magic_ai.checkout_message.enabled')
            && ! empty(core()->getConfigData('general.magic_ai.checkout_message.prompt'))
        ) {

            try {
                if (($model = core()->getConfigData('general.magic_ai.checkout_message.model')) == 'gpt-3.5-turbo') {
                    config([
                        'openai.api_key'      => core()->getConfigData('general.magic_ai.settings.api_key'),
                        'openai.organization' => core()->getConfigData('general.magic_ai.settings.organization'),
                    ]);

                    $result = OpenAI::chat()->create([
                        'model'       => 'gpt-3.5-turbo',
                        'temperature' => 0,
                        'messages'    => [
                            [
                                'role'    => 'user',
                                'content' => $this->getCheckoutPrompt($order),
                            ],
                        ],
                    ]);

                    $order->checkout_message = $result->choices[0]->message->content;
                } else {
                    $httpClient = new Client();

                    $endpoint = core()->getConfigData('general.magic_ai.settings.api_domain') . '/api/generate';

                    $result = $httpClient->request('POST', $endpoint, [
                        'headers' => [
                            'Accept' => 'application/json',
                        ],
                        'json'    => [
                            'model'  => $model,
                            'prompt' => $this->getCheckoutPrompt($order),
                            'raw'    => true,
                            'stream' => false,
                        ],
                    ]);

                    $result = json_decode($result->getBody()->getContents(), true);

                    $order->checkout_message = $result['response'];
                }
            } catch (\Exception $e) {
            }
        }

        return view('shop::checkout.success', compact('order'));
    }

    /**
     * Order success page.
     *
     * @param  \Webkul\Sales\Contracts\Order  $order
     * @return string
     */
    public function getCheckoutPrompt($order)
    {
        $prompt = core()->getConfigData('general.magic_ai.checkout_message.prompt');

        $products = '';

        foreach ($order->items as $item) {
            $products .= "Name: $item->name\n";
            $products .= "Qty: $item->qty_ordered\n";
            $products .= 'Price: ' . core()->formatPrice($item->total) . "\n\n";
        }

        $prompt .= "\n\nProduct Details:\n $products";

        $prompt .= "Customer Details:\n $order->customer_full_name \n\n";

        $prompt .= "Current Locale:\n " . core()->getCurrentLocale()->name . "\n\n";

        $prompt .= "Store Name:\n" . core()->getCurrentChannel()->name;

        return $prompt;
    }
}
