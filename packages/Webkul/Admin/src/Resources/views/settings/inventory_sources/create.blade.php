<x-admin::layouts>
    {{-- Title of the page --}}
    <x-slot:title>
        @lang('admin::app.settings.inventory-sources.create.add-title')
    </x-slot:title>

    {{-- Create Inventory --}}
    <v-inventory-create-form></v-inventory-create-form>

    @pushOnce('scripts')
        <script type="text/x-template" id="v-inventory-create-form-template">
            <div>
                <x-admin::form 
                    :action="route('admin.inventory_sources.store')"
                    enctype="multipart/form-data"
                >
                    <div class="flex gap-[16px] justify-between items-center max-sm:flex-wrap">
                        <p class="text-[20px] text-gray-800 font-bold">
                            @lang('admin::app.settings.inventory-sources.create.add-title')
                        </p>

                        <div class="flex gap-x-[10px] items-center">
                            <!-- Cancel Button -->
                            <a href="{{ route('admin.inventory_sources.index') }}">
                                <span class="text-gray-600 leading-[24px]">
                                    @lang('admin::app.marketing.email-marketing.campaigns.create.cancel')
                                </span>
                            </a>
            
                            <!-- Save Inventory -->
                            <button 
                                type="submit"
                                class="px-[12px] py-[6px] bg-blue-600 border border-blue-700 rounded-[6px] text-gray-50 font-semibold cursor-pointer"
                            >
                                @lang('admin::app.settings.inventory-sources.create.save-btn-title')
                            </button>
                        </div>
                    </div>

                    <!-- General -->
                    <div class="flex gap-[10px] mt-[14px] max-xl:flex-wrap">
                        <div class="flex flex-col gap-[8px] flex-1 max-xl:flex-auto">
                            <div class="p-[16px] bg-white rounded-[4px] box-shadow">

                                <p class="text-[16px] text-gray-800 font-semibold mb-[16px]">
                                    @lang('admin::app.settings.inventory-sources.create.general')
                                </p>

                                <x-admin::form.control-group class="mb-[10px]">
                                    <x-admin::form.control-group.label>
                                        @lang('admin::app.settings.inventory-sources.create.code')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="code"
                                        :value="old('code')"
                                        id="code"
                                        rules="required"
                                        :label="trans('admin::app.settings.inventory-sources.create.code')"
                                        :placeholder="trans('admin::app.settings.inventory-sources.create.code')"
                                    >
                                    </x-admin::form.control-group.control>

                                    <x-admin::form.control-group.error
                                        control-name="code"
                                    >
                                    </x-admin::form.control-group.error>
                                </x-admin::form.control-group>

                                <x-admin::form.control-group class="mb-[10px]">
                                    <x-admin::form.control-group.label>
                                        @lang('admin::app.settings.inventory-sources.create.name')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="name"
                                        :value="old('name')"
                                        id="name"
                                        rules="required"
                                        :label="trans('admin::app.settings.inventory-sources.create.name')"
                                        :placeholder="trans('admin::app.settings.inventory-sources.create.name')"
                                    >
                                    </x-admin::form.control-group.control>

                                    <x-admin::form.control-group.error
                                        control-name="name"
                                    >
                                    </x-admin::form.control-group.error>
                                </x-admin::form.control-group>

                                <x-admin::form.control-group class="mb-[10px]">
                                    <x-admin::form.control-group.label>
                                        @lang('admin::app.settings.inventory-sources.create.description')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="textarea"
                                        name="description"
                                        :value="old('description')"
                                        id="description"
                                        class="text-gray-600 !mb-[0px]"
                                        :label="trans('admin::app.settings.inventory-sources.create.description')"
                                        :placeholder="trans('admin::app.settings.inventory-sources.create.description')"
                                    >
                                    </x-admin::form.control-group.control>

                                    <x-admin::form.control-group.error
                                        control-name="description"
                                    >
                                    </x-admin::form.control-group.error>
                                </x-admin::form.control-group>

                                <x-admin::form.control-group class="mb-[10px]">
                                    <x-admin::form.control-group.label>
                                        @lang('admin::app.settings.inventory-sources.create.latitude')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="latitude"
                                        :value="old('latitude')"
                                        id="latitude"
                                        :label="trans('admin::app.settings.inventory-sources.create.latitude')"
                                        :placeholder="trans('admin::app.settings.inventory-sources.create.latitude')"
                                    >
                                    </x-admin::form.control-group.control>

                                    <x-admin::form.control-group.error
                                        control-name="latitude"
                                    >
                                    </x-admin::form.control-group.error>
                                </x-admin::form.control-group>

                                <x-admin::form.control-group class="mb-[10px]">
                                    <x-admin::form.control-group.label>
                                        @lang('admin::app.settings.inventory-sources.create.longitude')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="longitude"
                                        :value="old('longitude')"
                                        id="longitude"
                                        :label="trans('admin::app.settings.inventory-sources.create.longitude')"
                                        :placeholder="trans('admin::app.settings.inventory-sources.create.longitude')"
                                    >
                                    </x-admin::form.control-group.control>

                                    <x-admin::form.control-group.error
                                        control-name="longitude"
                                    >
                                    </x-admin::form.control-group.error>
                                </x-admin::form.control-group>

                                <x-admin::form.control-group class="mb-[10px]">
                                    <x-admin::form.control-group.label>
                                        @lang('admin::app.settings.inventory-sources.create.priority')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="priority"
                                        :value="old('priority')"
                                        id="priority"
                                        :label="trans('admin::app.settings.inventory-sources.create.priority')"
                                        :placeholder="trans('admin::app.settings.inventory-sources.create.priority')"
                                    >
                                    </x-admin::form.control-group.control>

                                    <x-admin::form.control-group.error
                                        control-name="priority"
                                    >
                                    </x-admin::form.control-group.error>
                                </x-admin::form.control-group>

                                <x-admin::form.control-group class="mb-[10px]">
                                    <x-admin::form.control-group.label>
                                        @lang('admin::app.settings.inventory-sources.create.status')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="switch"
                                        name="status"
                                        value="1"
                                        id="status"
                                        :label="trans('admin::app.settings.inventory-sources.create.status')"
                                        :placeholder="trans('admin::app.settings.inventory-sources.create.status')"
                                        :checked="old('status') ?? false"
                                    >
                                    </x-admin::form.control-group.control>

                                    <x-admin::form.control-group.error
                                        control-name="status"
                                    >
                                    </x-admin::form.control-group.error>
                                </x-admin::form.control-group>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="flex gap-[10px] mt-[14px] max-xl:flex-wrap">
                        <div class="flex flex-col gap-[8px] flex-1 max-xl:flex-auto">
                            <div class="p-[16px] bg-white rounded-[4px] box-shadow">

                                <p class="text-[16px] text-gray-800 font-semibold mb-[16px]">
                                    @lang('admin::app.settings.inventory-sources.create.contact-info')
                                </p>

                                <x-admin::form.control-group class="mb-[10px]">
                                    <x-admin::form.control-group.label>
                                        @lang('admin::app.settings.inventory-sources.create.contact-name')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="contact_name"
                                        :value="old('contact_name')"
                                        id="contact_name"
                                        rules="required"
                                        :label="trans('admin::app.settings.inventory-sources.create.contact-name')"
                                        :placeholder="trans('admin::app.settings.inventory-sources.create.contact-name')"
                                    >
                                    </x-admin::form.control-group.control>

                                    <x-admin::form.control-group.error
                                        control-name="contact_name"
                                    >
                                    </x-admin::form.control-group.error>
                                </x-admin::form.control-group>

                                <x-admin::form.control-group class="mb-[10px]">
                                    <x-admin::form.control-group.label>
                                        @lang('admin::app.settings.inventory-sources.create.contact-email')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="email"
                                        name="contact_email"
                                        :value="old('contact_email')"
                                        id="contact_email"
                                        rules="required|email"
                                        :label="trans('admin::app.settings.inventory-sources.create.contact-email')"
                                        :placeholder="trans('admin::app.settings.inventory-sources.create.contact-email')"
                                    >
                                    </x-admin::form.control-group.control>

                                    <x-admin::form.control-group.error
                                        control-name="contact_email"
                                    >
                                    </x-admin::form.control-group.error>
                                </x-admin::form.control-group>

                                <x-admin::form.control-group class="mb-[10px]">
                                    <x-admin::form.control-group.label>
                                        @lang('admin::app.settings.inventory-sources.create.contact-number')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="contact_number"
                                        :value="old('contact_number')"
                                        id="contact_number"
                                        rules="required"
                                        :label="trans('admin::app.settings.inventory-sources.create.contact-number')"
                                        :placeholder="trans('admin::app.settings.inventory-sources.create.contact-number')"
                                    >
                                    </x-admin::form.control-group.control>

                                    <x-admin::form.control-group.error
                                        control-name="contact_number"
                                    >
                                    </x-admin::form.control-group.error>
                                </x-admin::form.control-group>

                                <x-admin::form.control-group class="mb-[10px]">
                                    <x-admin::form.control-group.label>
                                        @lang('admin::app.settings.inventory-sources.create.contact-fax')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="contact_fax"
                                        :value="old('contact_fax')"
                                        id="contact_fax"
                                        :label="trans('admin::app.settings.inventory-sources.create.contact-fax')"
                                        :placeholder="trans('admin::app.settings.inventory-sources.create.contact-fax')"
                                    >
                                    </x-admin::form.control-group.control>

                                    <x-admin::form.control-group.error
                                        control-name="contact_fax"
                                    >
                                    </x-admin::form.control-group.error>
                                </x-admin::form.control-group>
                            </div>
                        </div>
                    </div>

                    <!-- Source Address -->
                    <div class="flex gap-[10px] mt-[14px] max-xl:flex-wrap">
                        <div class="flex flex-col gap-[8px] flex-1 max-xl:flex-auto">
                            <div class="p-[16px] bg-white rounded-[4px] box-shadow">

                                <p class="text-[16px] text-gray-800 font-semibold mb-[16px]">
                                    @lang('admin::app.settings.inventory-sources.create.address')
                                </p>

                                <x-admin::form.control-group class="mb-[10px]">
                                    <x-admin::form.control-group.label>
                                        @lang('admin::app.settings.inventory-sources.create.country')
                                    </x-admin::form.control-group.label>
                    
                                    <x-admin::form.control-group.control
                                        type="select"
                                        name="country"
                                        id="country"
                                        rules="required"
                                        :label="trans('admin::app.settings.inventory-sources.create.country')"
                                        :placeholder="trans('admin::app.settings.inventory-sources.create.country')"
                                        v-model="country"
                                    >
                                        <option value="">@lang('admin::app.settings.inventory-sources.create.select-country')</option>
                    
                                        @foreach (core()->countries() as $country)
                    
                                            <option value="{{ $country->code }}">{{ $country->name }}</option>
                    
                                        @endforeach
                                    </x-admin::form.control-group.control>
                    
                                    <x-admin::form.control-group.error
                                        control-name="country"
                                    >
                                    </x-admin::form.control-group.error>
                                </x-admin::form.control-group>
                                        
                                <x-admin::form.control-group class="mb-[10px]">
                                    <x-admin::form.control-group.label>
                                        @lang('admin::app.settings.inventory-sources.create.state')
                                    </x-admin::form.control-group.label>
                    
                                    <template v-if="haveStates()">
                                        <x-admin::form.control-group.control
                                            type="select"
                                            name="state"
                                            id="state"
                                            rules="required"
                                            :label="trans('admin::app.settings.inventory-sources.create.state')"
                                            :placeholder="trans('admin::app.settings.inventory-sources.create.state')"
                                        >
                                            <option value="">
                                                @lang('admin::app.settings.inventory-sources.create.select-state')
                                            </option>

                                            <option 
                                                v-for='(state, index) in countryStates[country]'
                                                :value="state.code"
                                                v-text="state.default_name"
                                            >
                                            </option>
                                        </x-admin::form.control-group.control>
                                    </template>
                    
                                    <template v-else>
                                        <x-admin::form.control-group.control
                                            type="text"
                                            name="state"
                                            :value="old('state')"
                                            id="state"
                                            rules="required"
                                            :label="trans('admin::app.settings.inventory-sources.create.state')"
                                            :placeholder="trans('admin::app.settings.inventory-sources.create.state')"
                                            v-model="state"
                                        >
                                        </x-admin::form.control-group.control>
                                    </template>

                                    <x-admin::form.control-group.error
                                        control-name="state"
                                    >
                                    </x-admin::form.control-group.error>
                                </x-admin::form.control-group>

                                <x-admin::form.control-group class="mb-[10px]">
                                    <x-admin::form.control-group.label>
                                        @lang('admin::app.settings.inventory-sources.create.city')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="city"
                                        :value="old('city')"
                                        id="city"
                                        rules="required"
                                        :label="trans('admin::app.settings.inventory-sources.create.city')"
                                        :placeholder="trans('admin::app.settings.inventory-sources.create.city')"
                                    >
                                    </x-admin::form.control-group.control>

                                    <x-admin::form.control-group.error
                                        control-name="city"
                                    >
                                    </x-admin::form.control-group.error>
                                </x-admin::form.control-group>

                                <x-admin::form.control-group class="mb-[10px]">
                                    <x-admin::form.control-group.label>
                                        @lang('admin::app.settings.inventory-sources.create.street')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="street"
                                        :value="old('street')"
                                        id="street"
                                        rules="required"
                                        :label="trans('admin::app.settings.inventory-sources.create.street')"
                                        :placeholder="trans('admin::app.settings.inventory-sources.create.street')"
                                    >
                                    </x-admin::form.control-group.control>

                                    <x-admin::form.control-group.error
                                        control-name="street"
                                    >
                                    </x-admin::form.control-group.error>
                                </x-admin::form.control-group>

                                <x-admin::form.control-group class="mb-[10px]">
                                    <x-admin::form.control-group.label>
                                        @lang('admin::app.settings.inventory-sources.create.postcode')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="text"
                                        name="postcode"
                                        :value="old('postcode')"
                                        id="postcode"
                                        rules="required"
                                        :label="trans('admin::app.settings.inventory-sources.create.postcode')"
                                        :placeholder="trans('admin::app.settings.inventory-sources.create.postcode')"
                                    >
                                    </x-admin::form.control-group.control>

                                    <x-admin::form.control-group.error
                                        control-name="postcode"
                                    >
                                    </x-admin::form.control-group.error>
                                </x-admin::form.control-group>
                            </div>
                        </div>
                    </div>

                    {!! view_render_event('bagisto.admin.settings.inventory.create.after') !!}
                </x-admin::form>
            </div>
        </script>

        <script type="module">
            app.component('v-inventory-create-form', {
                template: '#v-inventory-create-form-template',

                data: function () {
                    return {
                        country: "{{ old('country') }}",

                        state: "{{ old('state')  }}",

                        countryStates: @json(core()->groupedStatesByCountries())
                    }
                },

                methods: {
                    haveStates: function () {
                        /*
                        * The double negation operator is used to convert the value to a boolean.
                        * It ensures that the final result is a boolean value,
                        * true if the array has a length greater than 0, and otherwise false.
                        */
                        return !!this.countryStates[this.country]?.length;
                    },
                }
            })
        </script>
    @endpushOnce
</x-admin::layouts>