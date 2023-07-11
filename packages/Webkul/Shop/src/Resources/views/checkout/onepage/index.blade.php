<x-shop::layouts
    :has-header="false"
    :has-feature="false"
    :has-footer="false"
>
    <div class="bs-dekstop-menu flex flex-wrap">
        <div
            class="w-full flex justify-between px-[60px] border border-t-0 border-b-[1px] border-l-0 border-r-0 pb-[5px] pt-[17px] max-lg:px-[30px] max-sm:px-[15px]"
        >
            <div class="flex items-center gap-x-[54px] max-[1180px]:gap-x-[35px]">
                <a
                    href="{{ route('shop.home.index') }}" 
                    class="bs-logo bg-[position:-5px_-3px] bs-main-sprite w-[131px] h-[29px] inline-block mb-[16px]"
                >
                </a>
            </div>
        </div>
    </div>

    <div class="container px-[60px] max-lg:px-[30px] max-sm:px-[15px]">
        {{-- Breadcrumbs --}}
        <x-shop::breadcrumbs name="checkout"></x-shop::breadcrumbs>

        <v-checkout>
            {{-- Shimmer Effect --}}
            <x-shop::shimmer.checkout.onepage></x-shop::shimmer.checkout.onepage>
        </v-checkout>
    </div>

    @pushOnce('scripts')
        <script type="text/x-template" id="v-checkout-template">
            <div class="grid grid-cols-[1fr_auto] gap-[30px] max-lg:grid-cols-[1fr]">
                <div    
                    class="overflow-y-auto"
                    ref="scrollBottom"
                >
                    @include('shop::checkout.onepage.addresses.index')

                    @include('shop::checkout.onepage.shipping')

                    @include('shop::checkout.onepage.payment')

                </div>
                
                @include('shop::checkout.onepage.summary')
            </div>
        </script>

        <script type="module">
            app.component('v-checkout', {
                template: '#v-checkout-template',

                data() {
                    return {
                        cart: {},

                        isCartLoading: true,
                    }
                },

                created() {
                    this.getOrderSummary();
                }, 

                methods: {
                    getOrderSummary() {
                        this.$axios.get("{{ route('shop.checkout.onepage.summary') }}")
                            .then(response => {
                                this.cart = response.data.data;

                                this.isCartLoading = false;

                                let container = this.$refs.scrollBottom;

                                if (container) {
                                    container.scrollIntoView({
                                        behavior: 'smooth',
                                        block: 'end'
                                    });
                                }
                            })
                            .catch(error => console.log(error));
                    },
                },
            });
        </script>
    @endPushOnce
</x-shop::layouts>
