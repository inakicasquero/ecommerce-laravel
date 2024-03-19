{!! view_render_event('bagisto.admin.sales.order.create.cart_items.before') !!}

<!-- Vue JS Component -->
<v-previous-cart-items
    :cart="cart"
    @added-to-cart="getCart"
    @remove-from-cart="getCart"
>
    <!-- Items Shimmer Effect -->
    <x-admin::shimmer.sales.orders.create.items />
</v-previous-cart-items>

{!! view_render_event('bagisto.admin.sales.order.create.cart_items.after') !!}


@pushOnce('scripts')
    <script type="text/x-template" id="v-previous-cart-items-template">
        <template v-if="isLoading">
            <!-- Items Shimmer Effect -->
            <x-admin::shimmer.sales.orders.create.items />
        </template>

        <template v-else>
            <div class="bg-white dark:bg-gray-900 rounded box-shadow">
                <div class="flex justify-between items-center p-4">
                    <p class="text-base text-gray-800 dark:text-white font-semibold">
                        @lang('admin::app.sales.orders.create.cart-items.title')
                    </p>
                </div>

                <!-- cart items -->
                <div
                    class="grid"
                    v-if="items.length"
                >
                    <div
                        class="row flex gap-2.5 p-4 bg-white dark:bg-gray-900 border-b dark:border-gray-800 transition-all hover:bg-gray-50 dark:hover:bg-gray-950"
                        v-for="item in items"
                    >
                        <!-- Image -->
                        <div
                            class="w-full h-[60px] max-w-[60px] max-h-[60px] relative rounded overflow-hidden"
                            :class="{'border border-dashed border-gray-300 dark:border-gray-800 rounded dark:invert dark:mix-blend-exclusion overflow-hidden': ! item.images.length}"
                        >
                            <template v-if="! item.images.length">
                                <img src="{{ bagisto_asset('images/product-placeholders/front.svg') }}">
                            
                                <p class="w-full absolute bottom-1.5 text-[6px] text-gray-400 text-center font-semibold">
                                    @lang('admin::app.catalog.products.edit.types.grouped.image-placeholder')
                                </p>
                            </template>

                            <template v-else>
                                <img :src="item.images[0].url">
                            </template>
                        </div>

                        <!-- Item Information -->
                        <div class="grid gap-1.5">
                            <!-- Item Name -->
                            <p class="text-base text-gray-800 dark:text-white font-semibold">
                                @{{ item.name }}
                            </p>

                            <!-- Item SKU -->
                            <p class="text-gray-600 dark:text-gray-300">
                                @{{ "@lang('admin::app.sales.orders.create.cart-items.sku', ['sku' => ':replace'])".replace(':replace', item.sku) }}
                            </p>

                            <!-- Item Options -->
                            <p class="text-gray-600 dark:text-gray-300 [&>*]:after:content-['_,_']">
                                <span
                                    class="after:content-[','] last:after:content-['']"
                                    v-for="option in item.additional.attributes"
                                >
                                    @{{ option.attribute_name + ' : ' + option.option_label }}
                                </span>
                            </p>

                            <!-- Price -->
                            <p class="text-base text-gray-800 dark:text-white font-semibold">
                                @{{ item.formatted_price }}
                            </p>

                            <!-- Item Actions -->
                            <div class="flex gap-2.5 mt-2">
                                <p
                                    class="text-red-600 cursor-pointer transition-all hover:underline"
                                    @click="removeCartItem(item)"
                                >
                                    @lang('admin::app.sales.orders.create.cart-items.delete')
                                </p>

                                <p class="text-emerald-600 cursor-pointer transition-all hover:underline">
                                    @lang('admin::app.sales.orders.create.cart-items.move-to-cart')
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty Items Box -->
                <div
                    class="grid gap-3.5 justify-center justify-items-center py-10 px-2.5"
                    v-else
                >
                    <img src="{{ bagisto_asset('images/icon-add-product.svg') }}" class="w-20 h-20 dark:invert dark:mix-blend-exclusion">
                    
                    <div class="flex flex-col gap-1.5 items-center">
                        <p class="text-base text-gray-400 font-semibold">
                            @lang('admin::app.sales.orders.create.cart-items.empty-title')
                        </p>
    
                        <p class="text-gray-400">
                            @lang('admin::app.sales.orders.create.cart-items.empty-description')
                        </p>
                    </div>
                </div>
            </div>
        </template>
    </script>

    <script type="module">
        app.component('v-previous-cart-items', {
            template: '#v-previous-cart-items-template',

            props: ['cart'],

            data() {
                return {
                    isLoading: false,

                    items: [],
                };
            },

            mounted() {
                this.get();
            },

            methods: {
                get() {
                    this.isLoading = true;

                    this.$axios.get("{{ route('admin.customers.customers.cart.items', $cart->customer_id) }}")
                        .then(response => {
                            this.items = response.data.data;

                            this.isLoading = false;
                        })
                        .catch(error => {});
                }
            }
        });
    </script>
@endPushOnce