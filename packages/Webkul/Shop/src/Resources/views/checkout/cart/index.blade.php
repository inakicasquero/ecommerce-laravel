<x-shop::layouts
    :has-feature="false"
    :has-footer="false"
>
    <div class="flex-auto">
        <div class="container px-[60px] max-lg:px-[30px]">
            <x-shop::breadcrumbs name="cart"></x-shop::breadcrumbs>

            <v-cart ref="vCart">
                <x-shop::shimmer.checkout.cart :count="3"></x-shop::shimmer.checkout.cart>
            </v-cart>
        </div>
    </div>

    @pushOnce('scripts')
        <script type="text/x-template" id="v-cart-template">
            <div>
                <template v-if="isLoading">
                    <x-shop::shimmer.checkout.cart :count="3"></x-shop::shimmer.checkout.cart>
                </template>

                <template v-else>
                    <div 
                        class="flex flex-wrap gap-[75px] mt-[30px] pb-[30px]" 
                        v-if="cart?.items?.length"
                    >
                        <div class="grid gap-[30px] flex-1">
                            <div 
                                class="grid gap-y-[25px]" 
                                v-for="item in cart?.items"
                            >
                                <div class="flex gap-x-[10px] justify-between flex-wrap border-b-[1px] border-[#E9E9E9] pb-[18px]">
                                    <div class="flex gap-x-[20px]">
                                        <x-shop::shimmer.image
                                            class="w-[110px] h-[110px] rounded-[12px]"
                                            ::src="item.base_image.small_image_url"
                                        >
                                        </x-shop::shimmer.image>

                                        <div class="grid gap-y-[10px]">
                                            <p 
                                                class="text-[16px] font-medium" 
                                                v-text="item.name"
                                            >
                                            </p>
                                    
                                            <div
                                                class="flex gap-x-[10px] gap-y-[6px] flex-wrap"
                                                v-if="item.options.length"
                                            >
                                                <div class="grid gap-[8px]">
                                                    <div v-for="option in item.options">
                                                        <p 
                                                            class="text-[14px] font-medium" 
                                                            v-text="option.attribute_name + ':'"
                                                        >
                                                        </p>
                                    
                                                        <p class="text-[14px]" v-text="option.option_label"></p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="sm:hidden">
                                                <p 
                                                    class="text-[18px] font-semibold" 
                                                    v-text="item.formatted_total"
                                                >
                                                </p>
                                                
                                                <span
                                                    class="text-[16px] text-[#4D7EA8] cursor-pointer" 
                                                    @click="removeItem(item.id)"
                                                >
                                                    @lang('shop::app.checkout.cart.index.remove')
                                                </span>
                                            </div>

                                            <x-shop::quantity-changer
                                                name="quantity"
                                                ::value="item?.quantity"
                                                class="flex gap-x-[20px] border rounded-[54px] border-navyBlue py-[5px] px-[14px] items-center max-w-[108px] max-h-[36px]"
                                                @change="setItemQuantity(item.id, $event)"
                                            >
                                            </x-shop::quantity-changer>
                                        </div>
                                    </div>

                                    <div class="max-sm:hidden">
                                        <p 
                                            class="text-[18px] font-semibold" 
                                            v-text="item.formatted_total"
                                        >
                                        </p>
                                        
                                        <span
                                            class="text-[16px] text-[#4D7EA8] cursor-pointer" 
                                            @click="removeItem(item.id)"
                                        >
                                            @lang('shop::app.checkout.cart.index.remove')
                                        </span>
                                    </div>
                                </div>
                            </div>
        
                            <div class="flex flex-wrap gap-[30px] justify-end">
                                <a
                                    class="bs-secondary-button rounded-[18px] max-h-[55px]"
                                    href="{{ route('shop.home.index') }}"
                                >
                                    @lang('shop::app.checkout.cart.index.continue-shopping')
                                </a> 

                                <a 
                                    class="bs-secondary-button rounded-[18px] max-h-[55px]"
                                    @click="update()"
                                >
                                    @lang('shop::app.checkout.cart.index.update-cart')
                                </a>
                            </div>
                        </div>

                        @include('shop::checkout.cart.summary')
                    </div>

                    <div
                        class="grid items-center justify-items-center w-max m-auto h-[476px] place-content-center"
                        v-else
                    >
                        <img src="{{ bagisto_asset('images/thank-you.png') }}"/>
                        
                        <p class="text-[20px]">@lang('shop::app.checkout.cart.index.empty-product')</p>
                    </div>
                </template>
            </div>
        </script>

        <script type="module">
            app.component("v-cart", {
                template: '#v-cart-template',

                data() {
                    return  {
                        cart: [],

                        applied: {
                            quantity: {},
                        },

                        isLoading: true,
                    }
                },

                mounted() {
                    this.get();
                },

                methods: {
                    get() {
                        this.$axios.get('{{ route('shop.api.checkout.cart.index') }}')
                            .then(response => {
                                this.isLoading = false;

                                this.cart = response.data.data;
                            })
                            .catch(error => {});     
                    },

                    update() {
                        this.$axios.put('{{ route('shop.api.checkout.cart.update') }}', { qty: this.applied.quantity })
                            .then(response => {
                                this.cart = response.data.data;

                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                            })
                            .catch(error => {});
                    },

                    setItemQuantity(itemId, quantity) {
                        this.applied.quantity[itemId] = quantity;
                    },

                    removeItem(itemId) {
                        this.$axios.post('{{ route('shop.api.checkout.cart.destroy') }}', {
                                '_method': 'DELETE',
                                'cart_item_id': itemId,
                            })
                            .then(response => {
                                this.cart = response.data.data;

                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                            })
                            .catch(error => {});
                    },
                }
            });
        </script>
    @endpushOnce
</x-shop::layouts>
