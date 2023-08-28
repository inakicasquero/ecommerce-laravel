@php
    $admin = auth()->guard('admin')->user();

    $orderStatusMessages = [
        'pending'         => trans('admin::app.notification.order-status-messages.pending'),
        'canceled'        => trans('admin::app.notification.order-status-messages.canceled'),
        'closed'          => trans('admin::app.notification.order-status-messages.closed'),
        'completed'       => trans('admin::app.notification.order-status-messages.completed'),
        'processing'      => trans('admin::app.notification.order-status-messages.processing'),
        'pending_payment' => trans('admin::app.notification.order-status-messages.pending_payment')
    ];

    $allLocales = core()->getAllLocales()->pluck('name', 'code');
@endphp

<header class="flex justify-between items-center px-[16px] py-[10px] bg-white border-b-[1px] border-gray-300 sticky top-0 z-10">
    <div class="flex gap-[6px] items-center">
        {{-- Hamburger Menu --}}
        <i
            class="hidden icon-menu text-[24px] p-[6px] max-lg:block cursor-pointer"
            @click="$refs.sidebarMenuDrawer.open()"
        ></i>

        {{-- Logo --}}
        <a
            href="{{ route('admin.dashboard.index') }}" 
            class="place-self-start -mt-[4px]"            
        >
            @if (core()->getConfigData('general.design.admin_logo.logo_image', core()->getCurrentChannelCode()))
                <img src="{{ Storage::url(core()->getConfigData('general.design.admin_logo.logo_image', core()->getCurrentChannelCode())) }}" alt="{{ config('app.name') }}" style="height: 40px; width: 110px;"/>
            @else
                <img src="{{ bagisto_asset('images/logo.png') }}">
            @endif
        </a>

        {{-- Mega Search Bar Vue Component --}}
        <v-mega-search>
            <div class="flex items-center relative w-[525px] max-w-[525px] ml-[10px]">
                <i class="icon-search text-[22px] flex items-center absolute left-[12px] top-[6px]"></i>

                <input 
                    type="text" 
                    class="bg-white border border-gray-300 rounded-lg block w-full px-[40px] py-[5px] leading-6 text-gray-600 transition-all hover:border-gray-400"
                    placeholder="@lang('admin::app.components.layouts.header.mega-search.title')" 
                >
            </div>
        </v-mega-search>
    </div>

    <div class="flex gap-[10px] items-center">
        <a 
            href="{{ route('shop.home.index') }}" 
            target="_blank"
            class="mt-[6px]"
        >
            <span 
                class="icon-store p-[6px] rounded-[6px] text-[24px] cursor-pointer transition-all hover:bg-gray-100"
                title="@lang('admin::app.components.layouts.header.visit-shop')"
            >
            </span>
        </a>

        <x-admin::notification
            notif-title="{{ __('admin::app.notification.notification-title', ['read' => 0]) }}"
            :get-notification-url="route('admin.notification.get_notification')"
            :view-all="route('admin.notification.index')"
            order-view-url="{{ \URL::to('/') }}/{{ config('app.admin_url')}}/viewed-notifications/"
            :pusher-key="env('PUSHER_APP_KEY')"
            :pusher-cluster="env('PUSHER_APP_CLUSTER')"
            title="{{ __('admin::app.notification.title-plural') }}"
            view-all-title="{{ __('admin::app.notification.view-all') }}"
            :get-read-all-url="route('admin.notification.read_all')"
            :order-status-messages="json_encode($orderStatusMessages)"
            read-all-title="{{ __('admin::app.notification.read-all') }}"
            :locale-code="core()->getCurrentLocale()->code"
        >
        </x-admin::notification>

        {{-- Admin profile --}}
        <x-admin::dropdown position="bottom-{{ core()->getCurrentLocale()->direction === 'ltr' ? 'right' : 'left' }}">
            <x-slot:toggle>
                @if ($admin->image)
                    <div class="profile-info-icon">
                        <img
                            src="{{ $admin->image_url }}"
                            class="max-w-[36px] max-h-[36px] rounded-[6px]"
                        />
                    </div>
                @else
                    <div class="profile-info-icon">
                        <span class="px-[8px] py-[6px] bg-blue-400 rounded-full text-white font-semibold cursor-pointer leading-6">
                            {{ substr($admin->name, 0, 1) }}
                        </span>
                    </div>
                @endif
            </x-slot:toggle>

            {{-- Admin Dropdown --}}
            <x-slot:content class="!p-[0px]">
                <div class="grid gap-[10px] px-[20px] py-[10px] border border-b-gray-300">
                    {{-- Version --}}
                    <p class="text-gray-400">
                        @lang('admin::app.components.layouts.header.app-version', ['version' => 'v' . core()->version()])
                    </p>
                </div>

                <div class="grid gap-[4px] pb-[10px]">
                    <a
                        class="px-5 py-2 text-[16px] text-gray-800 hover:bg-gray-100 cursor-pointer"
                        href="{{ route('admin.account.edit') }}"
                    >
                        @lang('admin::app.components.layouts.header.my-account')
                    </a>

                    {{--Admin logout--}}
                    <x-admin::form
                        method="DELETE"
                        action="{{ route('admin.session.destroy') }}"
                        id="adminLogout"
                    >
                    </x-admin::form>

                    <a
                        class="px-5 py-2 text-[16px] text-gray-800 hover:bg-gray-100 cursor-pointer"
                        href="{{ route('admin.session.destroy') }}"
                        onclick="event.preventDefault(); document.getElementById('adminLogout').submit();"
                    >
                        @lang('admin::app.components.layouts.header.logout')
                    </a>
                </div>
            </x-slot:content>
        </x-admin::dropdown>
    </div>
</header>

<!-- Menu Sidebar Drawer -->
<x-admin::drawer
    position="left"
    width="270px"
    ref="sidebarMenuDrawer"
>
    <!-- Drawer Header -->
    <x-slot:header>
        <div class="flex justify-between items-center">
            @if (core()->getConfigData('general.design.admin_logo.logo_image', core()->getCurrentChannelCode()))
                <img src="{{ Storage::url(core()->getConfigData('general.design.admin_logo.logo_image', core()->getCurrentChannelCode())) }}" alt="{{ config('app.name') }}" style="height: 40px; width: 110px;"/>
            @else
                <img src="{{ bagisto_asset('images/logo.png') }}">
            @endif
        </div>
    </x-slot:header>

    <!-- Drawer Content -->
    <x-slot:content class="p-[16px]">
        <div class="h-[calc(100vh-100px)] overflow-auto journal-scroll">
            <nav class="grid gap-[7px] w-full">
                {{-- Navigation Menu --}}
                @foreach ($menu->items as $menuItem)
                    <div class="relative group/item">
                        <a
                            href="{{ $menuItem['url'] }}"
                            class="flex gap-[10px] p-[6px] items-center cursor-pointer {{ $menu->getActive($menuItem) == 'active' ? 'bg-blue-600 rounded-[8px]' : ' hover:bg-gray-100' }} peer"
                        >
                            <span class="{{ $menuItem['icon'] }} text-[24px] {{ $menu->getActive($menuItem) ? 'text-white' : ''}}"></span>
                            
                            <p class="text-gray-600 font-semibold whitespace-nowrap {{ $menu->getActive($menuItem) ? 'text-white' : ''}}">
                                @lang($menuItem['name'])
                            </p>
                        </a>

                        @if (count($menuItem['children']))
                            <div class="{{ $menu->getActive($menuItem) ? ' !grid bg-gray-100' : '' }} hidden min-w-[180px] pl-[40px] pb-[7px] rounded-b-[8px] z-[100]">
                                @foreach ($menuItem['children'] as $subMenuItem)
                                    <a
                                        href="{{ $subMenuItem['url'] }}"
                                        class="text-[14px] text-{{ $menu->getActive($subMenuItem) ? 'blue':'gray' }}-600 whitespace-nowrap py-[4px]"
                                    >
                                        @lang($subMenuItem['name'])
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </nav>
        </div>
    </x-slot:content>
</x-admin::drawer>

@pushOnce('scripts')
    <script type="text/x-template" id="v-mega-search-template">
        <div class="flex items-center relative w-[525px] max-w-[525px] ml-[10px]">
            <i class="icon-search text-[22px] flex items-center absolute left-[12px] top-[6px]"></i>

            <input 
                type="text" 
                class="bg-white border border-gray-300 rounded-lg block w-full px-[40px] py-[5px] leading-6 text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 peer"
                :class="{'border-gray-400': isDropdownOpen}"
                placeholder="@lang('admin::app.components.layouts.header.mega-search.title')"
                v-model.lazy="searchTerm"
                @click="searchTerm.length >= 2 ? isDropdownOpen = true : {}"
                v-debounce="500"
            >

            <div
                class="absolute top-[40px] w-full bg-white shadow-[0px_0px_0px_0px_rgba(0,0,0,0.10),0px_1px_3px_0px_rgba(0,0,0,0.10),0px_5px_5px_0px_rgba(0,0,0,0.09),0px_12px_7px_0px_rgba(0,0,0,0.05),0px_22px_9px_0px_rgba(0,0,0,0.01),0px_34px_9px_0px_rgba(0,0,0,0.00)] border border-gray-300 rounded-[8px] z-10"
                v-if="isDropdownOpen"
            >
                <!-- Search Tabs -->
                <div class="flex border-b-[1px] border-gray-300 text-[14px] text-gray-600">
                    <div
                        class="p-[16px] hover:bg-gray-100 cursor-pointer"
                        :class="{ 'border-b-[2px] border-blue-600': activeTab == tab.key }"
                        v-for="tab in tabs"
                        @click="activeTab = tab.key; search();"
                    >
                        @{{ tab.title }}
                    </div>
                </div>

                <!-- Searched Results -->
                <template v-if="activeTab == 'products'">
                    <template v-if="isLoading">
                        <x-admin::shimmer.header.mega-search.products></x-admin::shimmer.header.mega-search.products>
                    </template>

                    <template v-else>
                        <div class="grid max-h-[400px] overflow-y-auto">
                            <a
                                :href="'{{ route('admin.catalog.products.edit', ':id') }}'.replace(':id', product.id)"
                                class="flex gap-[10px] justify-between p-[16px] border-b-[1px] border-slate-300 cursor-pointer hover:bg-gray-100 last:border-b-0"
                                v-for="product in searchedResults.products.data"
                            >
                                <!-- Left Information -->
                                <div class="flex gap-[10px]">
                                    <!-- Image -->
                                    <div
                                        class="w-full h-[46px] max-w-[46px] max-h-[46px] relative rounded-[4px] overflow-hidden"
                                        :class="{'border border-dashed border-gray-300': ! product.images.length}"
                                    >
                                        <template v-if="! product.images.length">
                                            <img src="{{ bagisto_asset('images/product-placeholders/front.svg') }}">
                                        
                                            <p class="w-full absolute bottom-[5px] text-[6px] text-gray-400 text-center font-semibold">Product Image</p>
                                        </template>

                                        <template v-else>
                                            <img :src="product.images[0].url">
                                        </template>
                                    </div>

                                    <!-- Details -->
                                    <div class="grid gap-[6px] place-content-start">
                                        <p class="text-[16x] text-gray-600 font-semibold">
                                            @{{ product.name }}
                                        </p>

                                        <p class="text-gray-500">
                                            @{{ "@lang('admin::app.components.layouts.header.mega-search.sku')".replace(':sku', product.sku) }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Right Information -->
                                <div class="grid gap-[4px] place-content-center text-right">
                                    <p class="text-gray-600 font-semibold">
                                        @{{ product.formatted_price }}
                                    </p>
                                </div>
                            </a>
                        </div>

                        <div class="p-[12px] border-t-[1px] border-gray-300">
                            <a
                                :href="'{{ route('admin.catalog.products.index') }}?search=:query'.replace(':query', searchTerm)"
                                class=" text-[12px] text-blue-600 font-semibold cursor-pointer"
                                v-if="searchedResults.products.data.length"
                            >
                                @{{ "@lang('admin::app.components.layouts.header.mega-search.explore-all-matching-products')".replace(':query', searchTerm).replace(':count', searchedResults.products.total) }}
                            </a>

                            <a
                                href="{{ route('admin.catalog.products.index') }}"
                                class=" text-[12px] text-blue-600 font-semibold cursor-pointer"
                                v-else
                            >
                                @lang('admin::app.components.layouts.header.mega-search.explore-all-products')
                            </a>
                        </div>
                    </template>
                </template>

                <template v-if="activeTab == 'orders'">
                    <template v-if="isLoading">
                        <x-admin::shimmer.header.mega-search.orders></x-admin::shimmer.header.mega-search.orders>
                    </template>

                    <template v-else>
                        <div class="grid max-h-[400px] overflow-y-auto">
                            <a
                                :href="'{{ route('admin.sales.orders.view', ':id') }}'.replace(':id', order.id)"
                                class="grid gap-[6px] place-content-start p-[16px] border-b-[1px] border-slate-300 cursor-pointer hover:bg-gray-100 last:border-b-0"
                                v-for="order in searchedResults.orders.data"
                            >
                                <p class="text-[16x] text-gray-600 font-semibold">
                                    #@{{ order.increment_id }}
                                </p>

                                <p class="text-gray-500">
                                    @{{ order.formatted_created_at + ', ' + order.status_label + ', ' + order.customer_full_name }}
                                </p>
                            </a>
                        </div>

                        <div class="p-[12px] border-t-[1px] border-gray-300">
                            <a
                                :href="'{{ route('admin.sales.orders.index') }}?search=:query'.replace(':query', searchTerm)"
                                class=" text-[12px] text-blue-600 font-semibold cursor-pointer"
                                v-if="searchedResults.orders.data.length"
                            >
                                @{{ "@lang('admin::app.components.layouts.header.mega-search.explore-all-matching-orders')".replace(':query', searchTerm).replace(':count', searchedResults.orders.total) }}
                            </a>

                            <a
                                href="{{ route('admin.sales.orders.index') }}"
                                class=" text-[12px] text-blue-600 font-semibold cursor-pointer"
                                v-else
                            >
                                @lang('admin::app.components.layouts.header.mega-search.explore-all-orders')
                            </a>
                        </div>
                    </template>
                </template>

                <template v-if="activeTab == 'categories'">
                    <template v-if="isLoading">
                        <x-admin::shimmer.header.mega-search.categories></x-admin::shimmer.header.mega-search.categories>
                    </template>

                    <template v-else>
                        <div class="grid max-h-[400px] overflow-y-auto">
                            <a
                                :href="'{{ route('admin.catalog.categories.edit', ':id') }}'.replace(':id', category.id)"
                                class="p-[16px] border-b-[1px] border-gray-300 text-[14px] text-gray-600 font-semibold cursor-pointer hover:bg-gray-100 last:border-b-0"
                                v-for="category in searchedResults.categories.data"
                            >
                                @{{ category.name }}
                            </a>
                        </div>

                        <div class="p-[12px] border-t-[1px] border-gray-300">
                            <a
                                :href="'{{ route('admin.catalog.categories.index') }}?search=:query'.replace(':query', searchTerm)"
                                class=" text-[12px] text-blue-600 font-semibold cursor-pointer"
                                v-if="searchedResults.categories.data.length"
                            >
                                @{{ "@lang('admin::app.components.layouts.header.mega-search.explore-all-matching-categories')".replace(':query', searchTerm).replace(':count', searchedResults.categories.total) }}
                            </a>

                            <a
                                href="{{ route('admin.catalog.categories.index') }}"
                                class=" text-[12px] text-blue-600 font-semibold cursor-pointer"
                                v-else
                            >
                                @lang('admin::app.components.layouts.header.mega-search.explore-all-categories')
                            </a>
                        </div>
                    </template>
                </template>

                <template v-if="activeTab == 'customers'">
                    <template v-if="isLoading">
                        <x-admin::shimmer.header.mega-search.customers></x-admin::shimmer.header.mega-search.customers>
                    </template>

                    <template v-else>
                        <div class="grid max-h-[400px] overflow-y-auto">
                            <a
                                :href="'{{ route('admin.customers.customer.view', ':id') }}'.replace(':id', customer.id)"
                                class="grid gap-[6px] place-content-start p-[16px] border-b-[1px] border-slate-300 cursor-pointer hover:bg-gray-100 last:border-b-0"
                                v-for="customer in searchedResults.customers.data"
                            >
                                <p class="text-[16x] text-gray-600 font-semibold">
                                    @{{ customer.first_name + ' ' + customer.last_name }}
                                </p>

                                <p class="text-gray-500">
                                    @{{ customer.email }}
                                </p>
                            </a>
                        </div>

                        <div class="p-[12px] border-t-[1px] border-gray-300">
                            <a
                                :href="'{{ route('admin.customers.customer.index') }}?search=:query'.replace(':query', searchTerm)"
                                class=" text-[12px] text-blue-600 font-semibold cursor-pointer"
                                v-if="searchedResults.customers.data.length"
                            >
                                @{{ "@lang('admin::app.components.layouts.header.mega-search.explore-all-matching-customers')".replace(':query', searchTerm).replace(':count', searchedResults.customers.total) }}
                            </a>

                            <a
                                href="{{ route('admin.customer.index') }}"
                                class=" text-[12px] text-blue-600 font-semibold cursor-pointer"
                                v-else
                            >
                                @lang('admin::app.components.layouts.header.mega-search.explore-all-customers')
                            </a>
                        </div>
                    </template>
                </template>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-mega-search', {
            template: '#v-mega-search-template',

            data() {
                return {
                    activeTab: 'products',

                    isDropdownOpen: false,

                    tabs: {
                        products: {
                            key: 'products',
                            title: "@lang('admin::app.components.layouts.header.mega-search.products')",
                            is_active: true,
                            endpoint: "{{ route('admin.catalog.products.search') }}"
                        },
                        
                        orders: {
                            key: 'orders',
                            title: "@lang('admin::app.components.layouts.header.mega-search.orders')",
                            endpoint: "{{ route('admin.sales.orders.search') }}"
                        },
                        
                        categories: {
                            key: 'categories',
                            title: "@lang('admin::app.components.layouts.header.mega-search.categories')",
                            endpoint: "{{ route('admin.catalog.categories.search') }}"
                        },
                        
                        customers: {
                            key: 'customers',
                            title: "@lang('admin::app.components.layouts.header.mega-search.customers')",
                            endpoint: "{{ route('admin.customers.customer.search') }}"
                        }
                    },

                    isLoading: false,

                    searchTerm: '',

                    searchedResults: {
                        products: [],
                        orders: [],
                        categories: [],
                        customers: []
                    },
                }
            },

            watch: {
                searchTerm: function(newVal, oldVal) {
                    this.search()
                }
            },

            created() {
                window.addEventListener('click', this.handleFocusOut);
            },

            beforeDestroy() {
                window.removeEventListener('click', this.handleFocusOut);
            },

            methods: {
                search() {
                    if (this.searchTerm.length <= 1) {
                        this.searchedResults[this.activeTab] = [];

                        this.isDropdownOpen = false;

                        return;
                    }

                    this.isDropdownOpen = true;

                    let self = this;

                    this.isLoading = true;
                    
                    this.$axios.get(this.tabs[this.activeTab].endpoint, {
                            params: {query: this.searchTerm}
                        })
                        .then(function(response) {
                            self.searchedResults[self.activeTab] = response.data;

                            self.isLoading = false;
                        })
                        .catch(function (error) {
                        })
                },

                handleFocusOut(e) {
                    if (! this.$el.contains(e.target)) {
                        this.isDropdownOpen = false;
                    }
                },
            }
        });
    </script>
@endpushOnce