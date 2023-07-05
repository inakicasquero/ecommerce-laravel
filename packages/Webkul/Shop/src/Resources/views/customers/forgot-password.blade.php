<x-shop::layouts
    :has-header="false"
    :has-feature="false"
    :has-footer="false"
>
    <div class="container mt-20 max-1180:px-[20px]">
        {{-- Company Logo --}}
        <div class="flex items-center gap-x-[54px] max-[1180px]:gap-x-[35px]">
            <a
                href="{{ route('shop.home.index') }}" 
                class="m-[0_auto_20px_auto]"
            >
                <img src="{{ bagisto_asset('images/logo.png') }}">
            </a>
        </div>
        
        {{-- Form Container --}}
        <div
            class="w-full max-w-[870px] m-auto border border-[#E9E9E9] px-[90px] py-[60px] rounded-[12px] max-md:px-[30px] max-md:py-[30px]"
        >
            <h1 class="text-[40px] font-dmserif max-sm:text-[25px]">
                @lang('shop::app.customers.forgot-password.title')
            </h1>

            <p class="text-[#7D7D7D] text-[20px] mt-[15px] max-sm:text-[16px]">
                @lang('shop::app.customers.forgot-password.forgot-password-text')
            </p>

            {!! view_render_event('bagisto.shop.customers.forget_password.before') !!}

            <div class="rounded mt-[60px] max-sm:mt-[30px]">
                <x-shop::form :action="route('shop.customers.forgot_password.store')">
                    {!! view_render_event('bagisto.shop.customers.forget_password_form_controls.before') !!}

                    <x-shop::form.control-group class="mb-4">
                        <x-shop::form.control-group.label>
                            @lang('shop::app.customers.login-form.email')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control
                            type="email"
                            name="email"
                            class="!p-[20px_527px_20px_25px] rounded-lg"
                            value=""
                            rules="required|email"
                            label="Email"
                            placeholder="email@example.com"
                        >
                        </x-shop::form.control-group.control>

                        <x-shop::form.control-group.error
                            control-name="email"
                        >
                        </x-shop::form.control-group.error>
                    </x-shop::form.control-group>

                    {!! view_render_event('bagisto.shop.customers.forget_password_form_controls.before') !!}

                    <div>

                        {!! Captcha::render() !!}

                    </div>

                    <div class="flex gap-[36px] flex-wrap mt-[30px] items-center">
                        <button
                            class="m-0 ml-[0px] block mx-auto w-full bg-navyBlue text-white text-[16px] max-w-[374px] font-medium py-[16px] px-[43px] rounded-[18px] text-center"
                            type="submit"
                        >
                            @lang('shop::app.customers.forgot-password.submit')
                        </button>
                    </div>

                    <p class="text-[#7D7D7D] font-medium mt-[20px]">
                        @lang('shop::app.customers.forgot-password.back')
                        
                        <a class="text-navyBlue" 
                            href="{{ route('shop.customer.session.index') }}"
                        >
                            @lang('shop::app.customers.forgot-password.sign-in-button')
                        </a>
                    </p>

                    {!! view_render_event('bagisto.shop.customers.forget_password.after') !!}

                </x-shop::form>
            </div>
        </div>

        <p class="mt-[30px] mb-[15px] text-center text-[#7d7d7d] text-xs">
            @lang('shop::app.customers.forgot-password.footer')
        </p>
    </div>

    @push('scripts')

        {!! Captcha::renderJS() !!}
    
    @endpush
</x-shop::layouts>