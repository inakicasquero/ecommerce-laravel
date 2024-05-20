<v-products-carousel
    src="{{ $src }}"
    title="{{ $title }}"
    navigation-link="{{ $navigationLink ?? '' }}"
>
    <x-shop::shimmer.products.carousel :navigation-link="$navigationLink ?? false" />
</v-products-carousel>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-products-carousel-template"
    >
        <div
            class="container mt-20 max-lg:px-8 max-sm:mt-7 max-sm:!px-4"
            v-if="! isLoading && products.length"
        >
            <div class="flex justify-between">
                <h2 class="font-dmserif text-3xl max-sm:text-xl">
                    @{{ title }}
                </h2>

                <div class="flex items-center justify-between gap-8">
                    
                    <a
                        :href="navigationLink"
                        v-if="navigationLink"
                    >
                        <p class="hidden items-center text-sm max-sm:flex">
                            @lang('shop::app.components.products.carousel.view-all')

                            <span class="icon-arrow-right text-sm"></span>
                        </p>
                    </a>

                    <span
                        class="icon-arrow-left-stylish rtl:icon-arrow-right-stylish inline-block cursor-pointer text-2xl max-sm:hidden"
                        role="button"
                        aria-label="@lang('shop::app.components.products.carousel.previous')"
                        tabindex="0"
                        @click="swipeLeft"
                    >
                    </span>

                    <span
                        class="icon-arrow-right-stylish rtl:icon-arrow-left-stylish inline-block cursor-pointer text-2xl max-sm:hidden"
                        role="button"
                        aria-label="@lang('shop::app.components.products.carousel.next')"
                        tabindex="0"
                        @click="swipeRight"
                    >
                    </span>
                </div>
            </div>

            <div
                ref="swiperContainer"
                class="flex gap-8 [&>*]:flex-[0] mt-10 overflow-auto scroll-smooth scrollbar-hide max-sm:mt-5 max-sm:gap-4 max-sm:whitespace-nowrap"
            >
                <x-shop::products.card
                    class="min-w-[291px] max-sm:h-fit max-sm:min-w-[198px]"
                    v-for="product in products"
                />
            </div>

            <a
                :href="navigationLink"
                class="secondary-button mx-auto mt-14 block w-max rounded-2xl px-11 py-3 text-center text-base max-sm:mt-0 max-sm:hidden max-sm:py-3.5"
                v-if="navigationLink"
            >
                @lang('shop::app.components.products.carousel.view-all')
            </a>
        </div>

        <!-- Product Card Listing -->
        <template v-if="isLoading">
            <x-shop::shimmer.products.carousel :navigation-link="$navigationLink ?? false" />
        </template>
    </script>

    <script type="module">
        app.component('v-products-carousel', {
            template: '#v-products-carousel-template',

            props: [
                'src',
                'title',
                'navigationLink',
            ],

            data() {
                return {
                    isLoading: true,

                    products: [],

                    offset: 323,
                };
            },

            mounted() {
                this.getProducts();
            },

            methods: {
                getProducts() {
                    this.$axios.get(this.src)
                        .then(response => {
                            this.isLoading = false;

                            this.products = response.data.data;
                        }).catch(error => {
                            console.log(error);
                        });
                },

                swipeLeft() {
                    const container = this.$refs.swiperContainer;

                    container.scrollLeft -= this.offset;
                },

                swipeRight() {
                    const container = this.$refs.swiperContainer;

                    // Check if scroll reaches the end
                    if (container.scrollLeft + container.clientWidth >= container.scrollWidth) {
                        // Reset scroll to the beginning
                        container.scrollLeft = 0;
                    } else {
                        // Scroll to the right
                        container.scrollLeft += this.offset;
                    }
                },
            },
        });
    </script>
@endPushOnce
