<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
    <head>
        <title>@yield('page_title')</title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <link rel="icon" sizes="16x16" href="{{ asset('vendor/webkul/ui/assets/images/favicon.ico') }}" />

        <link rel="stylesheet" href="{{ asset('vendor/webkul/admin/assets/css/admin.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/webkul/ui/assets/css/ui.css') }}">

        @yield('head')

        @yield('css')

    </head>

    <body onload="loadTinyMCE()">
        <div id="app">

            <flash-wrapper ref='flashes'></flash-wrapper>

            @include ('admin::layouts.nav-top')

            @include ('admin::layouts.nav-left')

            <div class="content-container">

                @yield('content-wrapper')

            </div>

        </div>

        <script type="text/javascript">
            window.flashMessages = [];
            @if($success = session('success'))
                window.flashMessages = [{'type': 'alert-success', 'message': "{{ $success }}" }];
            @elseif($warning = session('warning'))
                window.flashMessages = [{'type': 'alert-warning', 'message': "{{ $warning }}" }];
            @elseif($error = session('error'))
                window.flashMessages = [{'type': 'alert-error', 'message': "{{ $error }}" }];
            @endif

            window.serverErrors = [];
            @if(isset($errors))
                @if (count($errors))
                    window.serverErrors = @json($errors->getMessages());
                @endif
            @endif
        </script>

        <script type="text/javascript" src="{{ asset('vendor/webkul/admin/assets/js/admin.js') }}"></script>
        <script type="text/javascript" src="{{ asset('vendor/webkul/ui/assets/js/ui.js') }}"></script>
        @stack('scripts')

        <script src="{{ asset('js/tinyMCE/tinymce.min.js') }}"></script>

        <script>
            function loadTinyMCE() {
                tinymce.init({
                    selector: 'textarea',
                    height: 300,
                    width: 535,
                    plugins: 'image imagetools media wordcount save fullscreen',
                    toolbar1: 'formatselect | bold italic strikethrough forecolor backcolor | link | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent  | removeformat',
                    image_advtab: true,
                    templates: [
                        { title: 'Test template 1', content: 'Test 1' },
                        { title: 'Test template 2', content: 'Test 2' }
                    ],
                });
            }
        </script>

        <div class="modal-overlay"></div>
    </body>
</html>