{!! view_render_event('bagisto.shop.layout.footer.before') !!}

{{--
    The category repository is injected directly here because there is no way
    to retrieve it from the view composer, as this is an anonymous component.
--}}
@inject('themeCustomizationRepository', 'Webkul\Shop\Repositories\ThemeCustomizationRepository')

{{--
    This code needs to be refactored to reduce the amount of PHP in the Blade
    template as much as possible.
--}}
@php
    $footerLinks = $themeCustomizationRepository->findOneWhere([
        'type'   => 'footer_links',
        'status' => 1
    ]); 
    
    if ($footerLinks) {
        $footerLinks = json_decode($footerLinks->options, true);
    }
@endphp

<footer class=" bg-lightOrange mt-[36px] max-sm:mt-[30px]">
    <div class="flex justify-between p-[60px] gap-x-[25px] gap-y-[30px] max-1060:flex-wrap max-1060:flex-col-reverse max-sm:px-[15px]">
        <div class="flex gap-[85px] items-start flex-wrap max-1180:gap-[25px] max-1060:justify-between">
            @foreach ((array) $footerLinks as $footerLink)
                <ul class="grid gap-[20px] text-[14px]">
                    @foreach ($footerLink as $foterItem)
                        <li>
                            <a href="{{ $foterItem['url'] }}">
                                {{ $foterItem['title'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>    
            @endforeach
        </div>

        {{-- News Letter subscription --}}
        @if(core()->getConfigData('customer.settings.newsletter.subscription'))
            <div class="grid gap-[10px]">
                <p class="text-[30px] italic max-w-[288px] leading-[45px] text-navyBlue">
                    @lang('shop::app.components.layouts.footer.newsletter-text')
                </p>

                <p class="text-[12px]">
                    @lang('shop::app.components.layouts.footer.subscribe-stay-touch')
                </p>

                <x-shop::form
                    :action="route('shop.subscribe')"
                    class="rounded mt-[10px] max-sm:mt-[30px]"
                >
                    <label for="organic-search" class="sr-only">Search</label>

                    <div class="relative w-full">

                    <x-shop::form.control-group.control
                        type="email"
                        name="subscriber_email"
                        class="bg-[#F1EADF] w-[420px] max-w-full border-[2px] border-[#E9DECC] rounded-[12px] block px-[20px] py-[20px] text-xs font-medium pr-[110px] max-1060:w-full"
                        rules="required|email"
                        label="Email"
                        placeholder="email@example.com"
                    >
                    </x-shop::form.control-group.control>

                    <x-shop::form.control-group.error
                        control-name="subscriber_email"
                    >
                    </x-shop::form.control-group.error>

                        <button
                            type="submit"
                            class="w-max px-[26px] py-[13px] bg-white rounded-[12px] text-[12px] font-medium absolute top-[8px] rtl:left-[8px] ltr:right-[8px] flex items-center"
                        >
                            @lang('shop::app.components.layouts.footer.subscribe')
                        </button>
                    </div>
                </x-shop::form>
            </div>
        @endif
    </div>

    <div class="flex justify-between  px-[60px] py-[13px] bg-[#F1EADF]">
        <p class="text-[14px] text-[#7D7D7D]">
            @lang('shop::app.components.layouts.footer.footer-text')
        </p>
    </div>
</footer>

{!! view_render_event('bagisto.shop.layout.footer.after') !!}
