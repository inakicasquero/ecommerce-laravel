@inject('toolbarHelper', 'Webkul\Product\Helpers\Toolbar')

@extends('shop::customers.account.index')

@section('page_title')
    {{ __('shop::app.customer.account.wishlist.page-title') }}
@endsection

@section('page-detail-wrapper')
    <div class="account-head">
        <span class="account-heading">{{ __('shop::app.customer.account.wishlist.title') }}</span>

        @if (count($wishlistItems))
            <div class="account-action float-right">
                <form id="remove-all-wishlist" class="d-none" action="{{ route('customer.wishlist.removeall') }}" method="POST">
                    @method('DELETE')

                    @csrf
                </form>

                <a
                    class="remove-decoration theme-btn light"
                    href="javascript:void(0);"
                    onclick="document.getElementById('remove-all-wishlist').submit();">
                    {{ __('shop::app.customer.account.wishlist.deleteall') }}
                </a>
            </div>

            <div class="account-action float-right w-10">&nbsp;</div>

            <div class="account-action float-right">
                <a
                    class="remove-decoration theme-btn light"
                    href="javascript:void(0);"
                    @click="window.showShareWishlistModal();">
                    Share
                    {{-- {{ __('shop::app.customer.account.wishlist.share') }} --}}
                </a>
            </div>
        @endif
    </div>

    {!! view_render_event('bagisto.shop.customers.account.wishlist.list.before', ['wishlist' => $wishlistItems]) !!}

    <div class="wishlist-container">
        @if ($wishlistItems->count())
            @foreach ($wishlistItems as $wishlistItem)
                @include ('shop::customers.account.wishlist.wishlist-products', ['wishlistItem' => $wishlistItem])
            @endforeach

            <div>
                {{ $wishlistItems->links()  }}
            </div>
        @else
            <div class="empty">
                {{ __('customer::app.wishlist.empty') }}
            </div>
        @endif
    </div>

    <div id="shareWishlistModal" class="d-none">
        <modal id="shareWishlist" :is-open="modalIds.shareWishlist">
            <h3 slot="header">
                Share Wishlist
            </h3>

            <i class="rango-close"></i>

            <div slot="body">
                <form method="POST" action="{{ route('customer.wishlist.share') }}">
                    @csrf

                    <div class="row">
                        <div class="col-12">
                            <label class="mandatory">
                                Wishlist Sharing
                            </label>

                            <select name="shared" class="form-control">
                                <option value="0" {{ $isWishlistShared ? '' : 'selected="selected"' }}>Disable</option>
                                <option value="1" {{ $isWishlistShared ? 'selected="selected"' : '' }}>Enable</option>
                            </select>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-12">
                            <label class="mandatory">
                                Visibility
                            </label>

                            <div>
                                @if ($isWishlistShared)
                                    <span class="badge badge-success">Public</span>
                                @else
                                    <span class="badge badge-danger">Private</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-12">
                            <label class="mandatory">
                                Shared Link
                            </label>

                            <div>
                                @if ($isWishlistShared)
                                    <a href="{{ $wishlistSharedLink ?? 'javascript:void(0);' }}" target="_blank">{{ $wishlistSharedLink }}</a>
                                @else
                                    <p class="alert alert-danger">Enable wishlist sharing to get the link.</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-12">
                            <button type="submit"  class="theme-btn float-right">
                                Save
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </modal>
    </div>

    {!! view_render_event('bagisto.shop.customers.account.wishlist.list.after', ['wishlist' => $wishlistItems]) !!}
@endsection

@push('scripts')
    <script>
        /**
         * Show share wishlist modal.
         */
        function showShareWishlistModal() {
            document.getElementById('shareWishlistModal').classList.remove('d-none');

            window.app.showModal('shareWishlist');
        }
    </script>
@endpush