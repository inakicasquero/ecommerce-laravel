<?php

namespace Webkul\Core\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ChannelTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('channels')->delete();

        DB::table('channels')->insert([
            'id'                => 1,
            'code'              => 'default',
            'theme'             => 'default',
            'hostname'          => config('app.url'),
            'root_category_id'  => 1,
            'default_locale_id' => 1,
            'base_currency_id'  => 1,
        ]);

        DB::table('channel_translations')->insert([
            [
                'id'                => 1,
                'channel_id'        => 1,
                'locale'            => 'en',
                'name'              => 'Default',
                'home_page_content' => '',
                'footer_content'    => '
                    <ul class="grid gap-[20px] text-[14px]">
                        <li>
                            <a href="' . route('shop.cms.page', 'about-us') . '">
                                ' . trans('shop::app.footer.about-us') . '
                            </a>
                        </li>

                        <li>
                            <a href="' . route('shop.cms.page', 'customer-service') . '">
                                ' . trans('shop::app.footer.customer-service') . '
                            </a>
                        </li>

                        <li>
                            <a href="' . route('shop.cms.page', 'whats-new') . '">
                                ' . trans('shop::app.footer.whats-new') . '
                            </a>
                        </li>

                        <li>
                            <a href="' . route('shop.cms.page', 'contact-us') . '">
                                ' . trans('shop::app.footer.contact-us') . '
                            </a>
                        </li>
                    </ul>

                    <ul class="grid gap-[20px] text-[14px]">
                        <li>
                            <a href="' . route('shop.cms.page', 'order-return') . '">
                                ' . trans('shop::app.footer.order-return') . '
                            </a>
                        </li>

                        <li>
                            <a href="' . route('shop.cms.page', 'payment-policy') . '">
                                ' . trans('shop::app.footer.payment-policy') . '
                            </a>
                        </li>

                        <li>
                            <a href="' . route('shop.cms.page', 'shipping-policy') . '">
                                ' . trans('shop::app.footer.shipping-policy') . '
                            </a>
                        </li>

                        <li>
                            <a href="' . route('shop.cms.page', 'privacy-cookies-policy') . '">
                                ' . trans('shop::app.footer.privacy-cookies-policy') . '
                            </a>
                        </li>
                    </ul>
                ',
                'home_seo'          => '{"meta_title": "Demo store", "meta_keywords": "Demo store meta keyword", "meta_description": "Demo store meta description"}',
            ], [
                'id'                => 2,
                'channel_id'        => 1,
                'locale'            => 'fr',
                'name'              => 'Default',
                'home_page_content' => '
                    <p>@include("shop::home.slider") @include("shop::home.featured-products") @include("shop::home.new-products")</p>
                        <div class="banner-container">
                        <div class="left-banner">
                            <img src=' . asset('/themes/default/assets/images/1.webp') . ' data-src=' . asset('/themes/default/assets/images/1.webp') . ' class="lazyload" alt="test" width="720" height="720" />
                        </div>
                        <div class="right-banner">
                            <img src=' . asset('/themes/default/assets/images/2.webp') . ' data-src=' . asset('/themes/default/assets/images/2.webp') . ' class="lazyload" alt="test" width="460" height="330" />
                            <img src=' . asset('/themes/default/assets/images/3.webp') . ' data-src=' . asset('/themes/default/assets/images/3.webp') . '  class="lazyload" alt="test" width="460" height="330" />
                        </div>
                    </div>
                ',
                'footer_content'    => '
                    <ul class="grid gap-[20px] text-[14px]">
                        <li>
                            <a href="' . route('shop.cms.page', 'about-us') . '">
                                ' . trans('shop::app.footer.about-us') . '
                            </a>
                        </li>

                        <li>
                            <a href="' . route('shop.cms.page', 'customer-service') . '">
                                ' . trans('shop::app.footer.customer-service') . '
                            </a>
                        </li>

                        <li>
                            <a href="' . route('shop.cms.page', 'whats-new') . '">
                                ' . trans('shop::app.footer.whats-new') . '
                            </a>
                        </li>

                        <li>
                            <a href="' . route('shop.cms.page', 'contact-us') . '">
                                ' . trans('shop::app.footer.contact-us') . '
                            </a>
                        </li>
                    </ul>

                    <ul class="grid gap-[20px] text-[14px]">
                        <li>
                            <a href="' . route('shop.cms.page', 'order-return') . '">
                                ' . trans('shop::app.footer.order-return') . '
                            </a>
                        </li>

                        <li>
                            <a href="' . route('shop.cms.page', 'payment-policy') . '">
                                ' . trans('shop::app.footer.payment-policy') . '
                            </a>
                        </li>

                        <li>
                            <a href="' . route('shop.cms.page', 'shipping-policy') . '">
                                ' . trans('shop::app.footer.shipping-policy') . '
                            </a>
                        </li>

                        <li>
                            <a href="' . route('shop.cms.page', 'privacy-cookies-policy') . '">
                                ' . trans('shop::app.footer.privacy-cookies-policy') . '
                            </a>
                        </li>
                    </ul>
                ',
                'home_seo'          => '{"meta_title": "Demo store", "meta_keywords": "Demo store meta keyword", "meta_description": "Demo store meta description"}',
            ], [
                'id'                => 3,
                'channel_id'        => 1,
                'locale'            => 'nl',
                'name'              => 'Default',
                'home_page_content' => '
                    <p>@include("shop::home.slider") @include("shop::home.featured-products") @include("shop::home.new-products")</p>
                        <div class="banner-container">
                        <div class="left-banner">
                            <img src=' . asset('/themes/default/assets/images/1.webp') . ' data-src=' . asset('/themes/default/assets/images/1.webp') . ' class="lazyload" alt="test" width="720" height="720" />
                        </div>
                        <div class="right-banner">
                            <img src=' . asset('/themes/default/assets/images/2.webp') . ' data-src=' . asset('/themes/default/assets/images/2.webp') . ' class="lazyload" alt="test" width="460" height="330" />
                            <img src=' . asset('/themes/default/assets/images/3.webp') . ' data-src=' . asset('/themes/default/assets/images/3.webp') . '  class="lazyload" alt="test" width="460" height="330" />
                        </div>
                    </div>
                ',
                'footer_content'    => '
                    <ul class="grid gap-[20px] text-[14px]">
                        <li>
                            <a href="' . route('shop.cms.page', 'about-us') . '">
                                ' . trans('shop::app.footer.about-us') . '
                            </a>
                        </li>

                        <li>
                            <a href="' . route('shop.cms.page', 'customer-service') . '">
                                ' . trans('shop::app.footer.customer-service') . '
                            </a>
                        </li>

                        <li>
                            <a href="' . route('shop.cms.page', 'whats-new') . '">
                                ' . trans('shop::app.footer.whats-new') . '
                            </a>
                        </li>

                        <li>
                            <a href="' . route('shop.cms.page', 'contact-us') . '">
                                ' . trans('shop::app.footer.contact-us') . '
                            </a>
                        </li>
                    </ul>

                    <ul class="grid gap-[20px] text-[14px]">
                        <li>
                            <a href="' . route('shop.cms.page', 'order-return') . '">
                                ' . trans('shop::app.footer.order-return') . '
                            </a>
                        </li>

                        <li>
                            <a href="' . route('shop.cms.page', 'payment-policy') . '">
                                ' . trans('shop::app.footer.payment-policy') . '
                            </a>
                        </li>

                        <li>
                            <a href="' . route('shop.cms.page', 'shipping-policy') . '">
                                ' . trans('shop::app.footer.shipping-policy') . '
                            </a>
                        </li>

                        <li>
                            <a href="' . route('shop.cms.page', 'privacy-cookies-policy') . '">
                                ' . trans('shop::app.footer.privacy-cookies-policy') . '
                            </a>
                        </li>
                    </ul>
                ',
                'home_seo'          => '{"meta_title": "Demo store", "meta_keywords": "Demo store meta keyword", "meta_description": "Demo store meta description"}',
            ], [
                'id'                => 4,
                'channel_id'        => 1,
                'locale'            => 'tr',
                'name'              => 'Default',
                'home_page_content' => '
                    <p>@include("shop::home.slider") @include("shop::home.featured-products") @include("shop::home.new-products")</p>
                        <div class="banner-container">
                        <div class="left-banner">
                            <img src=' . asset('/themes/default/assets/images/1.webp') . ' data-src=' . asset('/themes/default/assets/images/1.webp') . ' class="lazyload" alt="test" width="720" height="720" />
                        </div>
                        <div class="right-banner">
                            <img src=' . asset('/themes/default/assets/images/2.webp') . ' data-src=' . asset('/themes/default/assets/images/2.webp') . ' class="lazyload" alt="test" width="460" height="330" />
                            <img src=' . asset('/themes/default/assets/images/3.webp') . ' data-src=' . asset('/themes/default/assets/images/3.webp') . '  class="lazyload" alt="test" width="460" height="330" />
                        </div>
                    </div>
                ',
                'footer_content'    => '
                    <ul class="grid gap-[20px] text-[14px]">
                        <li>
                            <a href="' . route('shop.cms.page', 'about-us') . '">
                                ' . trans('shop::app.footer.about-us') . '
                            </a>
                        </li>

                        <li>
                            <a href="' . route('shop.cms.page', 'customer-service') . '">
                                ' . trans('shop::app.footer.customer-service') . '
                            </a>
                        </li>

                        <li>
                            <a href="' . route('shop.cms.page', 'whats-new') . '">
                                ' . trans('shop::app.footer.whats-new') . '
                            </a>
                        </li>

                        <li>
                            <a href="' . route('shop.cms.page', 'contact-us') . '">
                                ' . trans('shop::app.footer.contact-us') . '
                            </a>
                        </li>
                    </ul>

                    <ul class="grid gap-[20px] text-[14px]">
                        <li>
                            <a href="' . route('shop.cms.page', 'order-return') . '">
                                ' . trans('shop::app.footer.order-return') . '
                            </a>
                        </li>

                        <li>
                            <a href="' . route('shop.cms.page', 'payment-policy') . '">
                                ' . trans('shop::app.footer.payment-policy') . '
                            </a>
                        </li>

                        <li>
                            <a href="' . route('shop.cms.page', 'shipping-policy') . '">
                                ' . trans('shop::app.footer.shipping-policy') . '
                            </a>
                        </li>

                        <li>
                            <a href="' . route('shop.cms.page', 'privacy-cookies-policy') . '">
                                ' . trans('shop::app.footer.privacy-cookies-policy') . '
                            </a>
                        </li>
                    </ul>
                ',
                'home_seo'          => '{"meta_title": "Demo store", "meta_keywords": "Demo store meta keyword", "meta_description": "Demo store meta description"}',
            ], [
                'id'                => 5,
                'channel_id'        => 1,
                'locale'            => 'es',
                'name'              => 'Default',
                'home_page_content' => '
                    <p>@include("shop::home.slider") @include("shop::home.featured-products") @include("shop::home.new-products")</p>
                        <div class="banner-container">
                        <div class="left-banner">
                            <img src=' . asset('/themes/default/assets/images/1.webp') . ' data-src=' . asset('/themes/default/assets/images/1.webp') . ' class="lazyload" alt="test" width="720" height="720" />
                        </div>
                        <div class="right-banner">
                            <img src=' . asset('/themes/default/assets/images/2.webp') . ' data-src=' . asset('/themes/default/assets/images/2.webp') . ' class="lazyload" alt="test" width="460" height="330" />
                            <img src=' . asset('/themes/default/assets/images/3.webp') . ' data-src=' . asset('/themes/default/assets/images/3.webp') . '  class="lazyload" alt="test" width="460" height="330" />
                        </div>
                    </div>
                ',
                'footer_content'    => '
                    <ul class="grid gap-[20px] text-[14px]">
                        <li>
                            <a href="' . route('shop.cms.page', 'about-us') . '">
                                ' . trans('shop::app.footer.about-us') . '
                            </a>
                        </li>

                        <li>
                            <a href="' . route('shop.cms.page', 'customer-service') . '">
                                ' . trans('shop::app.footer.customer-service') . '
                            </a>
                        </li>

                        <li>
                            <a href="' . route('shop.cms.page', 'whats-new') . '">
                                ' . trans('shop::app.footer.whats-new') . '
                            </a>
                        </li>

                        <li>
                            <a href="' . route('shop.cms.page', 'contact-us') . '">
                                ' . trans('shop::app.footer.contact-us') . '
                            </a>
                        </li>
                    </ul>

                    <ul class="grid gap-[20px] text-[14px]">
                        <li>
                            <a href="' . route('shop.cms.page', 'order-return') . '">
                                ' . trans('shop::app.footer.order-return') . '
                            </a>
                        </li>

                        <li>
                            <a href="' . route('shop.cms.page', 'payment-policy') . '">
                                ' . trans('shop::app.footer.payment-policy') . '
                            </a>
                        </li>

                        <li>
                            <a href="' . route('shop.cms.page', 'shipping-policy') . '">
                                ' . trans('shop::app.footer.shipping-policy') . '
                            </a>
                        </li>

                        <li>
                            <a href="' . route('shop.cms.page', 'privacy-cookies-policy') . '">
                                ' . trans('shop::app.footer.privacy-cookies-policy') . '
                            </a>
                        </li>
                    </ul>
                ',
                'home_seo'          => '{"meta_title": "Demo store", "meta_keywords": "Demo store meta keyword", "meta_description": "Demo store meta description"}',
            ],
        ]);

        DB::table('channel_currencies')->insert([
            'channel_id'  => 1,
            'currency_id' => 1,
        ]);

        DB::table('channel_locales')->insert([
            'channel_id' => 1,
            'locale_id'  => 1,
        ]);

        DB::table('channel_inventory_sources')->insert([
            'channel_id'          => 1,
            'inventory_source_id' => 1,
        ]);
    }
}
