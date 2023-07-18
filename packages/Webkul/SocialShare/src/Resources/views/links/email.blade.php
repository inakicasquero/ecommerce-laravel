@php
    $url = route('shop.productOrCategory.index', $product->url_key);

    $message = empty($message) ? $product->name : $message;

    $emailURL = 'mailto:your@email.com?subject=' . $message . '&body=' . $message . ' ' . $url;
@endphp

<v-email-share></v-email-share>

@push('scripts')
    <script type="text/x-template" id="v-email-share-template">
        <li class="transition-all hover:opacity-[0.8]">
            <a 
                href="{{ $emailURL }}" 
                target="_blank"
            >
                @include('social_share::icons.email')
            </a>
        </li>
    </script>

    <script type="module">
        app.component('v-email-share', {
            template: '#v-email-share-template'
        });
    </script>
@endpush