<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ core()->getCurrentLocale()->direction }}">

<head>
    <title>{{ $title ?? '' }}</title>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url()->to('/') }}">
    <meta name="currency-code" content="{{ core()->getCurrentCurrencyCode() }}">
    <meta http-equiv="content-language" content="{{ app()->getLocale() }}">

    @stack('meta')

    {{-- <link
        rel="icon"
        sizes="16x16"
        href="{{ core()->getCurrentChannel()->favicon_url ?? bagisto_asset('images/favicon.ico') }}"
    /> --}}

    @bagistoVite(['src/Resources/assets/css/app.css', 'src/Resources/assets/js/app.js'])

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    />

    <link
        href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&display=swap"
        rel="stylesheet"
    />

    @stack('styles')

    <style>
        {!! core()->getConfigData('general.content.custom_scripts.custom_css') !!}
    </style>

    {!! view_render_event('bagisto.shop.layout.head') !!}
</head>

<body>
    {!! view_render_event('bagisto.shop.layout.body.before') !!}

    <div id="app">
        {!! view_render_event('bagisto.shop.layout.content.before') !!}

        {{-- Page Content Blade Component --}}
        <x-admin::layouts.header/>

        <div class="flex gap-[16px]">
            <x-admin::layouts.sidebar/>
    
            {{ $slot }}
        
        </div>

        {!! view_render_event('bagisto.shop.layout.content.after') !!}
    </div>

    {!! view_render_event('bagisto.shop.layout.body.after') !!}

    <script type="text/javascript">
        window.serverErrors = [];

        @if (count($errors))
            window.serverErrors = @json($errors->getMessages());
        @endif
    </script>

    @stack('scripts')

    <script type="text/javascript">
        {!! core()->getConfigData('general.content.custom_scripts.custom_javascript') !!}
    </script>
</body>

</html>
