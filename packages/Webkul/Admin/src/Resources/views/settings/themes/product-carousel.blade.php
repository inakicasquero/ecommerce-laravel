<!-- Image-Carousel Component -->
<v-product-theme></v-product-theme>

@pushOnce('scripts')
    <script type="text/x-template" id="v-product-theme-template">
        <div class="flex gap-[10px] mt-[14px] max-xl:flex-wrap">
            <div class=" flex flex-col gap-[8px] flex-1 max-xl:flex-auto">
                <div class="p-[16px] bg-white dark:bg-gray-900 rounded box-shadow">
                    <div class="flex gap-x-[10px] justify-between items-center mb-[10px]">
                        <div class="flex flex-col gap-[4px]">
                            <p class="text-[16px] text-gray-800 dark:text-white font-semibold">
                                @lang('admin::app.settings.themes.edit.product-carousel')
                            </p>

                            <p class="text-[12px] text-gray-500 dark:text-gray-300 font-medium">
                                @lang('admin::app.settings.themes.edit.product-carousel-description')
                            </p>
                        </div>
                    </div>

                    <x-admin::form.control-group class="mb-[10px] pt-[16px]">
                        <x-admin::form.control-group.label class="required">
                            @lang('admin::app.settings.themes.edit.filter-title')
                        </x-admin::form.control-group.label>

                        <v-field
                            type="text"
                            name="options[title]"
                            value="{{ $theme->options['title'] ?? '' }}"
                            class="flex w-full min-h-[39px] py-2 px-3 border rounded-[6px] text-[14px] text-gray-600 dark:text-gray-300 transition-all hover:border-gray-400 dark:hover:border-gray-400 focus:border-gray-400 dark:focus:border-gray-400 dark:bg-gray-900 dark:border-gray-800"
                            :class="[errors['options[title]'] ? 'border border-red-600 hover:border-red-600' : '']"
                            rules="required"
                            label="@lang('admin::app.settings.themes.edit.filter-title')"
                            placeholder="@lang('admin::app.settings.themes.edit.filter-title')"
                        >
                        </v-field>

                        <x-admin::form.control-group.error
                            control-name="options[title]"
                        >
                        </x-admin::form.control-group.error>
                    </x-admin::form.control-group>

                    <x-admin::form.control-group class="mb-[10px]">
                        <x-admin::form.control-group.label class="required">
                            @lang('admin::app.settings.themes.edit.sort')
                        </x-admin::form.control-group.label>

                        <v-field
                            name="options[filters][sort]"
                            v-slot="{ field }"
                            rules="required"
                            value="{{ $theme->options['filters']['sort'] ?? '' }}"
                            label="@lang('admin::app.settings.themes.edit.sort')"
                        >
                            <select
                                name="options[filters][sort]"
                                v-bind="field"
                                class="custom-select flex w-full min-h-[39px] py-[6px] px-[12px] bg-white dark:bg-gray-900 border dark:border-gray-800 rounded-[6px] text-[14px] text-gray-600 dark:text-gray-300 font-normal transition-all hover:border-gray-400 dark:hover:border-gray-400"
                                :class="[errors['options[filters][sort]'] ? 'border border-red-600 hover:border-red-600' : '']"
                            >
                                <option value="" selected disabled>
                                    @lang('admin::app.settings.themes.edit.select')
                                </option>
                                
                                @foreach (
                                    product_toolbar()->getAvailableOrders()->pluck('title', 'value') 
                                    as $key => $availableOrder
                                )
                                    <option value="{{ $key }}">{{ $availableOrder }}</option>
                                @endforeach
                            </select>
                        </v-field>

                        <x-admin::form.control-group.error
                            control-name="options[filters][sort]"
                        >
                        </x-admin::form.control-group.error>
                    </x-admin::form.control-group>

                    <x-admin::form.control-group class="mb-[10px]">
                        <x-admin::form.control-group.label class="required">
                            @lang('admin::app.settings.themes.edit.limit')
                        </x-admin::form.control-group.label>

                        <v-field
                            type="select"
                            name="options[filters][limit]"
                            v-slot="{ field }"
                            rules="required"
                            value="{{ $theme->options['filters']['limit'] ?? '' }}"
                            label="@lang('admin::app.settings.themes.edit.limit')"
                        >
                            <select
                                name="options[filters][limit]"
                                v-bind="field"
                                class="custom-select flex w-full min-h-[39px] py-[6px] px-[12px] bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-800 rounded-[6px] text-[14px] text-gray-600 dark:text-gray-300 font-normal transition-all hover:border-gray-400 dark:hover:border-gray-400"
                                :class="[errors['options[filters][limit]'] ? 'border border-red-600 hover:border-red-600' : '']"
                            >
                                <option value="" selected disabled>@lang('admin::app.settings.themes.edit.select')</option>

                                @foreach (product_toolbar()->getAvailableLimits() as $availableLimit)
                                    <option value="{{ $availableLimit }}">{{ $availableLimit }}</option>
                                @endforeach
                            </select>
                        </v-field>

                        <x-admin::form.control-group.error
                            control-name="options[filters][limit]"
                        >
                        </x-admin::form.control-group.error>
                    </x-admin::form.control-group>

                    <span class="block w-full mb-[16px] mt-[16px] border-b-[1px] dark:border-gray-800"></span>

                    <div class="flex gap-x-[10px] justify-between items-center">
                        <div class="flex flex-col gap-[4px]">
                            <p class="text-[16px] text-gray-800 dark:text-white font-semibold">
                                @lang('admin::app.settings.themes.edit.filters')
                            </p>
                        </div>
        
                        <div class="flex gap-[10px]">
                            <div
                                class="secondary-button"
                                @click="$refs.productFilterModal.toggle()"
                            >
                                @lang('admin::app.settings.themes.edit.add-filter-btn')
                            </div>
                        </div>
                    </div>

                    <!-- Filters Lists -->
                    <div
                        class="grid"
                        v-if="options.filters.length"
                        v-for="(filter, index) in options.filters"
                    >
                        <!-- Hidden Input -->
                        <input
                            type="hidden"
                            :name="'options[filters][' + filter.key +']'"
                            :value="filter.value"
                        /> 
                    
                        <!-- Details -->
                        <div 
                            class="flex gap-[10px] justify-between py-5 cursor-pointer"
                            :class="{
                                'border-b-[1px] border-slate-300 dark:border-gray-800': index < options.filters.length - 1
                            }"
                        >
                            <div class="flex gap-[10px]">
                                <div class="grid gap-[6px] place-content-start">
                                    <p class="text-gray-600 dark:text-gray-300">
                                        <div> 
                                            @{{ "@lang('admin::app.settings.themes.edit.key')".replace(':key', filter.key) }}
                                        </div>
                                    </p>

                                    <p class="text-gray-600 dark:text-gray-300">
                                        @{{ "@lang('admin::app.settings.themes.edit.value')".replace(':value', filter.value) }}
                                    </p>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="grid gap-[4px] place-content-start text-right">
                                <div class="flex gap-x-[20px] items-center">
                                    <p 
                                        class="text-red-600 cursor-pointer transition-all hover:underline"
                                        @click="remove(filter)"
                                    > 
                                        @lang('admin::app.settings.themes.edit.delete')
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Filters Illustration -->
                    <div    
                        class="grid gap-[14px] justify-center justify-items-center py-[40px] px-[10px] "
                        v-else
                    >
                        <img
                            class="w-[120px] h-[120px] p-2 dark:invert dark:mix-blend-exclusion"
                            src="{{ bagisto_asset('images/empty-placeholders/default.svg') }}"
                            alt="add-product-to-store"
                        >
        
                        <div class="flex flex-col gap-[5px] items-center">
                            <p class="text-[16px] text-gray-400 font-semibold">
                                @lang('admin::app.settings.themes.edit.product-carousel')
                            </p>

                            <p class="text-gray-400">
                                @lang('admin::app.settings.themes.edit.product-carousel-description')
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- General -->
            <div class="flex flex-col gap-[8px] w-[360px] max-w-full max-sm:w-full">
                <x-admin::accordion>
                    <x-slot:header>
                        <p class="p-[10px] text-gray-600 dark:text-gray-300 text-[16px] font-semibold">
                            @lang('admin::app.settings.themes.edit.general')
                        </p>
                    </x-slot:header>
                
                    <x-slot:content>
                        <input type="hidden" name="type" value="product_carousel">

                        <x-admin::form.control-group class="mb-[10px]">
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.settings.themes.edit.name')
                            </x-admin::form.control-group.label>

                            <v-field
                                type="text"
                                name="name"
                                value="{{ $theme->name }}"
                                rules="required"
                                class="flex w-full min-h-[39px] py-2 px-3 border rounded-[6px] text-[14px] text-gray-600 dark:text-gray-300 transition-all hover:border-gray-400 dark:hover:border-gray-400 focus:border-gray-400 dark:focus:border-gray-400 dark:bg-gray-900 dark:border-gray-800"
                                :class="[errors['name'] ? 'border border-red-600 hover:border-red-600' : '']"
                                label="@lang('admin::app.settings.themes.edit.name')"
                                placeholder="@lang('admin::app.settings.themes.edit.name')"
                            >
                            </v-field>

                            <x-admin::form.control-group.error
                                control-name="name"
                            >
                            </x-admin::form.control-group.error>
                        </x-admin::form.control-group>

                        <x-admin::form.control-group class="mb-[10px]">
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.settings.themes.edit.sort-order')
                            </x-admin::form.control-group.label>

                            <v-field
                                type="text"
                                name="sort_order"
                                value="{{ $theme->sort_order }}"
                                rules="required|min_value:1"
                                class="flex w-full min-h-[39px] py-2 px-3 border rounded-[6px] text-[14px] text-gray-600 dark:text-gray-300 transition-all hover:border-gray-400 dark:hover:border-gray-400 focus:border-gray-400 dark:focus:border-gray-400 dark:bg-gray-900 dark:border-gray-800"
                                :class="[errors['sort_order'] ? 'border border-red-600 hover:border-red-600' : '']"
                                label="@lang('admin::app.settings.themes.edit.sort-order')"
                                placeholder="@lang('admin::app.settings.themes.edit.sort-order')"
                            >
                            </v-field>

                            <x-admin::form.control-group.error
                                control-name="sort_order"
                            >
                            </x-admin::form.control-group.error>
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.settings.themes.edit.channels')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="select"
                                name="channel_id"
                                rules="required"
                                :value="$theme->channel_id"
                            >
                                @foreach($channels as $channel)
                                    <option value="{{ $channel->id }}">{{ $channel->name }}</option>
                                @endforeach 
                            </x-admin::form.control-group.control>

                            <x-admin::form.control-group.error control-name="channel_id"></x-admin::form.control-group.error>
                        </x-admin::form.control-group>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.settings.themes.edit.status')
                            </x-admin::form.control-group.label>

                            <label class="relative inline-flex items-center cursor-pointer">
                                <v-field
                                    type="checkbox"
                                    name="status"
                                    class="hidden"
                                    v-slot="{ field }"
                                    value="{{ $theme->status }}"
                                >
                                    <input
                                        type="checkbox"
                                        name="status"
                                        id="status"
                                        class="sr-only peer"
                                        v-bind="field"
                                        :checked="{{ $theme->status }}"
                                    />
                                </v-field>
                    
                                <label
                                    class="rounded-full dark:peer-focus:ring-blue-800 peer-checked:bg-blue-600 w-[36px] h-[20px] bg-gray-200 cursor-pointer peer-focus:ring-blue-300 after:bg-white after:border-gray-300 peer-checked:bg-navyBlue peer peer-checked:after:border-white peer-checked:after:ltr:translate-x-full peer-checked:after:rtl:-translate-x-full after:content-[''] after:absolute after:top-[2px] after:ltr:left-[2px] after:rtl:right-[2px] peer-focus:outline-none after:border after:rounded-full after:h-[16px] after:w-[16px] after:transition-all"
                                    for="status"
                                ></label>
                            </label>

                            <x-admin::form.control-group.error
                                control-name="status"
                            >
                            </x-admin::form.control-group.error>
                        </x-admin::form.control-group>
                    </x-slot:content>
                </x-admin::accordion>
            </div>

            <!-- For Fitler Form -->
            <x-admin::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
            >
                <form @submit="handleSubmit($event, addFilter)">
                    <x-admin::modal ref="productFilterModal">
                        <x-slot:header>
                            <p class="text-[18px] text-gray-800 dark:text-white font-bold">
                                @lang('admin::app.settings.themes.edit.create-filter')
                            </p>
                        </x-slot:header>

                        <x-slot:content>
                            <div class="px-[16px] py-[10px] border-b-[1px] dark:border-gray-800">
                                <!-- Key -->
                                <x-admin::form.control-group class="mb-[10px]">
                                    <x-admin::form.control-group.label class="required">
                                        @lang('admin::app.settings.themes.edit.key-input')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="key"
                                        rules="required"
                                        :label="trans('admin::app.settings.themes.edit.key-input')"
                                        :placeholder="trans('admin::app.settings.themes.edit.key-input')"
                                    >
                                    </x-admin::form.control-group.control>

                                    <x-admin::form.control-group.error
                                        control-name="key"
                                    >
                                    </x-admin::form.control-group.error>
                                </x-admin::form.control-group>

                                <!-- Value -->
                                <x-admin::form.control-group class="mb-[10px]">
                                    <x-admin::form.control-group.label class="required">
                                        @lang('admin::app.settings.themes.edit.value-input')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="value"
                                        rules="required"
                                        :label="trans('admin::app.settings.themes.edit.value-input')"
                                        :placeholder="trans('admin::app.settings.themes.edit.value-input')"
                                    >
                                    </x-admin::form.control-group.control>

                                    <x-admin::form.control-group.error
                                        control-name="value"
                                    >
                                    </x-admin::form.control-group.error>
                                </x-admin::form.control-group>
                            </div>
                        </x-slot:content>

                        <x-slot:footer>
                            <div class="flex gap-x-[10px] items-center">
                                <!-- Save Button -->
                                <button 
                                    type="submit"
                                    class="px-[12px] py-[6px] bg-blue-600 border border-blue-700 rounded-[6px] text-gray-50 font-semibold cursor-pointer"
                                >
                                    @lang('admin::app.settings.themes.edit.save-btn')
                                </button>
                            </div>
                        </x-slot:footer>
                    </x-admin::modal>
                </form>
            </x-admin::form>
        </div>
    </script>

    <script type="module">
        app.component('v-product-theme', {
            template: '#v-product-theme-template',

            props: ['errors'],

            data() {
                return {
                    options: @json($theme->options),
                };
            },

            created() {
                if (this.options === null) {
                    this.options = { filters: {} };
                }   
                
                if (! this.options.filters) {
                    this.options.filters = {};
                }

                this.options.filters = Object.keys(this.options.filters)
                    .filter(key => ! ['sort', 'limit', 'title'].includes(key))
                    .map(key => ({
                        key: key,
                        value: this.options.filters[key]
                    }));
            },

            methods: {
                addFilter(params) {
                    this.options.filters.push(params);

                    this.$refs.productFilterModal.toggle();
                },

                remove(filter) {
                    let index = this.options.filters.indexOf(filter);

                    this.options.filters.splice(index, 1);
                },
            },
        });
    </script>
@endPushOnce    