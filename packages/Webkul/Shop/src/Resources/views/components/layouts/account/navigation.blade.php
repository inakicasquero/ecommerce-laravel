<!--
    - This code needs to be refactored to reduce the amount of PHP in the Blade
    template as much as possible.

    - Need to check the view composer capability for the component.
-->
@php
    $menu = \Webkul\Core\Tree::create();

    foreach (config('menu.customer') as $item) {
        $menu->add($item, 'menu');
    }

    $menu->items = core()->sortItems($menu->items);

    $customer = auth()->guard('customer')->user();
@endphp

<div class="panel-side journal-scroll max-md::gap-5 grid max-h-[1320px] min-w-[342px] max-w-[380px] grid-cols-[1fr] gap-8 overflow-y-auto overflow-x-hidden max-xl:min-w-[270px] max-md:max-w-full">
    <!-- Account Profile Hero Section -->
    <div class="max-md::py-2.5 grid grid-cols-[auto_1fr] items-center gap-4 rounded-xl border border-zinc-200 px-5 py-[25px]">
        <div class="">
            <img
                src="{{ $customer->image_url ??  bagisto_asset('images/user-placeholder.png') }}"
                class="h-[60px] w-[60px] rounded-full"
                alt="Profile Image"
            >
        </div>

        <div class="flex flex-col justify-between">
            <p class="font-mediums max-md::text-xl text-2xl">Hello! {{ $customer->first_name }}</p>

            <p class="max-md::text-md: text-zinc-500 no-underline">{{ $customer->email }}</p>
        </div>
    </div>

    <!-- Account Navigation Menus -->
    @foreach ($menu->items as $menuItem)
        <div>
            <!-- Account Navigation Toggler -->
            <div class="max-md::pb-1.5 select-none pb-5">
                <p class="text-xl font-medium">@lang($menuItem['name'])</p>
            </div>

            <!-- Account Navigation Content -->
            <div class="grid rounded-md border border-b border-l-[1px] border-r border-t-0 border-zinc-200 max-md:border-none">
                @if (! (bool) core()->getConfigData('general.content.shop.wishlist_option'))
                    @php
                        unset($menuItem['children']['wishlist']);
                    @endphp
                @endif

                @foreach ($menuItem['children'] as $subMenuItem)
                    <a href="{{ $subMenuItem['url'] }}">
                        <div class="flex justify-between px-6 py-5 border-t border-zinc-200 hover:bg-zinc-100 cursor-pointer max-md::p-4 max-md:border-0 max-md::py-3 max-md::px-0 {{ request()->routeIs($subMenuItem['route']) ? 'bg-zinc-100' : '' }}">
                            <p class="max-md::text-base flex items-center gap-x-4 text-lg font-medium">
                                <span class="{{ $subMenuItem['icon'] }}  text-2xl"></span>

                                @lang($subMenuItem['name'])
                            </p>

                            <span class="icon-arrow-right rtl:icon-arrow-left text-2xl"></span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endforeach
</div>