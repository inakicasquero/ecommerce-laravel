<v-create-customer-address></v-create-customer-address>

    {{-- Customer Address Modal --}}
    @pushOnce('scripts')
        <!-- Customer Address Form -->
        <script type="text/x-template" id="v-create-customer-address-template">
            <div>
                <!-- Address Create Button -->
                @if (bouncer()->hasPermission('customers.addresses.create '))
                    <div 
                        class="inline-flex gap-x-[8px] items-center justify-between w-full max-w-max px-[4px] py-[6px] text-gray-600 font-semibold text-center  cursor-pointer transition-all hover:bg-gray-200 hover:rounded-[6px]"
                        @click="$refs.CustomerAddress.toggle()"
                    >
                        <span class="icon-location text-[24px]"></span>
                        @lang('admin::app.customers.addresses.create.create-address-btn')
                    </div>
                @endif

                <x-admin::form
                    v-slot="{ meta, errors, handleSubmit }"
                    as="div"
                >
                    <form @submit="handleSubmit($event, create)">
                        <!-- Address Create Modal -->
                        <x-admin::modal ref="CustomerAddress">
                        
                            <x-slot:header>
                                <!-- Modal Header -->
                                <p class="text-[18px] text-gray-800 font-bold">
                                    @lang('admin::app.customers.addresses.create.title')
                                </p>    
                            </x-slot:header>
            
                            <x-slot:content>
                                <!-- Modal Content -->
                                {!! view_render_event('admin.customer.addresses.create.before') !!}

                                <x-admin::form.control-group class="mb-[10px]">
                                    <x-admin::form.control-group.control
                                        type="hidden"
                                        name="customer_id"
                                        :value="$customer->id"
                                    >
                                    </x-admin::form.control-group.control>
                                </x-admin::form.control-group>

                                <div class="px-[16px] py-[10px] border-b-[1px] border-gray-300">
                                    <div class="flex gap-[16px] max-sm:flex-wrap">
                                        <div class="w-full">
                                            <!-- Company Name -->
                                            <x-admin::form.control-group class="mb-[10px]">
                                                <x-admin::form.control-group.label>
                                                        @lang('admin::app.customers.addresses.create.company-name')
                                                </x-admin::form.control-group.label>

                                                <x-admin::form.control-group.control
                                                    type="text"
                                                    name="company_name"
                                                    :label="trans('admin::app.customers.addresses.create.company-name')"
                                                    :placeholder="trans('admin::app.customers.addresses.create.company-name')"
                                                >
                                                </x-admin::form.control-group.control>

                                                <x-admin::form.control-group.error
                                                    control-name="company_name"
                                                >
                                                </x-admin::form.control-group.error>
                                            </x-admin::form.control-group>
                                        </div>
                                        <div class="w-full">
                                            <!-- Vat Id -->
                                            <x-admin::form.control-group class="mb-[10px]">
                                                <x-admin::form.control-group.label>
                                                    @lang('admin::app.customers.addresses.create.vat-id')
                                                </x-admin::form.control-group.label>
            
                                                <x-admin::form.control-group.control
                                                    type="text"
                                                    name="vat_id"
                                                    :label="trans('admin::app.customers.addresses.create.vat-id')"
                                                    :placeholder="trans('admin::app.customers.addresses.create.vat-id')"
                                                >
                                                </x-admin::form.control-group.control>
            
                                                <x-admin::form.control-group.error
                                                    control-name="vat_id"
                                                >
                                                </x-admin::form.control-group.error>
                                            </x-admin::form.control-group>
                                        </div>
                                    </div>

                                    <div class="flex gap-[16px] max-sm:flex-wrap">
                                        <div class="w-full">
                                            <!-- First Name -->
                                            <x-admin::form.control-group class="mb-[10px]">
                                                <x-admin::form.control-group.label>
                                                    @lang('admin::app.customers.addresses.create.first-name')
                                                </x-admin::form.control-group.label>

                                                <x-admin::form.control-group.control
                                                    type="text"
                                                    name="first_name"
                                                    rules="required"
                                                    :label="trans('admin::app.customers.addresses.create.first-name')"
                                                    :placeholder="trans('admin::app.customers.addresses.create.first-name')"
                                                >
                                                </x-admin::form.control-group.control>

                                                <x-admin::form.control-group.error
                                                    control-name="first_name"
                                                >
                                                </x-admin::form.control-group.error>
                                            </x-admin::form.control-group>
                                        </div>
                                        <div class="w-full">
                                            <!-- Last Name -->
                                            <x-admin::form.control-group class="mb-[10px]">
                                                <x-admin::form.control-group.label>
                                                    @lang('admin::app.customers.addresses.create.last-name')
                                                </x-admin::form.control-group.label>
            
                                                <x-admin::form.control-group.control
                                                    type="text"
                                                    name="last_name"
                                                    rules="required"
                                                    :label="trans('admin::app.customers.addresses.create.last-name')"
                                                    :placeholder="trans('admin::app.customers.addresses.create.last-name')"
                                                >
                                                </x-admin::form.control-group.control>
            
                                                <x-admin::form.control-group.error
                                                    control-name="last_name"
                                                >
                                                </x-admin::form.control-group.error>
                                            </x-admin::form.control-group>
                                        </div>
                                    </div>

                                    <!-- Street Address -->
                                    <x-admin::form.control-group class="mb-[10px]">
                                        <x-admin::form.control-group.label>
                                            @lang('admin::app.customers.addresses.create.street-address')
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="text"
                                            name="address1[]"
                                            id="address_0"
                                            rules="required"
                                            :label="trans('admin::app.customers.addresses.create.street-address')"
                                            :placeholder="trans('admin::app.customers.addresses.create.street-address')"
                                        >
                                        </x-admin::form.control-group.control>

                                        <x-admin::form.control-group.error
                                            control-name="address1[]"
                                        >
                                        </x-admin::form.control-group.error>
                                    </x-admin::form.control-group>

                                    <!--need to check this -->
                                    @if (
                                        core()->getConfigData('customer.address.information.street_lines')
                                        && core()->getConfigData('customer.address.information.street_lines') > 1
                                    )
                                        <div v-for="(address, index) in addressLines" :key="index">
                                            <x-admin::form.control-group class="mb-[10px]">
                                            <x-admin::form.control-group.label>
                                                @lang('admin::app.customers.addresses.create.street-address')
                                            </x-admin::form.control-group.label>
                                    
                                            <x-admin::form.control-group.control
                                                type="text"
                                                :name="'address1[' + index + ']'"
                                                :id="'address_' + index"
                                                rules="required"
                                                :label="trans('admin::app.customers.addresses.create.street-address')"
                                                :placeholder="trans('admin::app.customers.addresses.create.street-address')"
                                            >
                                            </x-admin::form.control-group.control>
                                    
                                            <x-admin::form.control-group.error
                                                :control-name="'address1[' + index + ']'"
                                            >
                                            </x-admin::form.control-group.error>
                                            </x-admin::form.control-group>
                                        </div>
                                    @endif

                                    <div class="flex gap-[16px] max-sm:flex-wrap">
                                        <div class="w-full">
                                            <!-- City -->
                                            <x-admin::form.control-group class="mb-[10px]">
                                                <x-admin::form.control-group.label>
                                                    @lang('admin::app.customers.addresses.create.city')
                                                </x-admin::form.control-group.label>

                                                <x-admin::form.control-group.control
                                                    type="text"
                                                    name="city"
                                                    rules="required"
                                                    :label="trans('admin::app.customers.addresses.create.city')"
                                                    :placeholder="trans('admin::app.customers.addresses.create.city')"
                                                >
                                                </x-admin::form.control-group.control>

                                                <x-admin::form.control-group.error
                                                    control-name="city"
                                                >
                                                </x-admin::form.control-group.error>
                                            </x-admin::form.control-group>
                                        </div>
                                        <div class="w-full">
                                            <!-- PostCode -->
                                            <x-admin::form.control-group class="mb-[10px]">
                                                <x-admin::form.control-group.label>
                                                    @lang('admin::app.customers.addresses.create.post-code')
                                                </x-admin::form.control-group.label>
            
                                                <x-admin::form.control-group.control
                                                    type="text"
                                                    name="postcode"
                                                    rules="required|integer"
                                                    :label="trans('admin::app.customers.addresses.create.post-code')"
                                                    :placeholder="trans('admin::app.customers.addresses.create.post-code')"
                                                >
                                                </x-admin::form.control-group.control>
            
                                                <x-admin::form.control-group.error
                                                    control-name="postcode"
                                                >
                                                </x-admin::form.control-group.error>
                                            </x-admin::form.control-group>
                                        </div>
                                    </div>

                                    <div class="flex gap-[16px] max-sm:flex-wrap">
                                        <div class="w-full">
                                            <!-- Country Name -->
                                            <x-admin::form.control-group class="mb-[10px]">
                                                <x-admin::form.control-group.label>
                                                    @lang('admin::app.customers.addresses.create.country')
                                                </x-admin::form.control-group.label>

                                                <x-admin::form.control-group.control
                                                    type="select"
                                                    name="country"
                                                    rules="required"
                                                    :label="trans('admin::app.customers.addresses.create.country')"
                                                >
                                                    <option value="">@lang('Select Country')</option>

                                                    @foreach (core()->countries() as $country)
                                                        <option 
                                                            {{ $country->code === config('app.default_country') ? 'selected' : '' }}  
                                                            value="{{ $country->code }}"
                                                        >
                                                            {{ $country->name }}
                                                        </option>
                                                    @endforeach
                                                </x-admin::form.control-group.control>

                                                <x-admin::form.control-group.error
                                                    control-name="country"
                                                >
                                                </x-admin::form.control-group.error>
                                            </x-admin::form.control-group>
                                        </div>
                                        <div class="w-full">
                                            <!-- State Name -->
                                            <x-admin::form.control-group class="mb-[10px]">
                                                <x-admin::form.control-group.label>
                                                    @lang('admin::app.customers.addresses.create.state')
                                                </x-admin::form.control-group.label>

                                                <x-admin::form.control-group.control
                                                    type="text"
                                                    name="state"
                                                    rules="required"
                                                    :label="trans('admin::app.customers.addresses.create.state')"
                                                    :placeholder="trans('admin::app.customers.addresses.create.state')"
                                                >
                                                </x-admin::form.control-group.control>

                                                <x-admin::form.control-group.error
                                                    control-name="state"
                                                >
                                                </x-admin::form.control-group.error>
                                            </x-admin::form.control-group>
                                        </div>
                                    </div>

                                    <div class="flex gap-[16px] max-sm:flex-wrap items-center">
                                        <div class="w-full">
                                            <!--Phone number -->
                                            <x-admin::form.control-group class="mb-[10px]">
                                                <x-admin::form.control-group.label>
                                                    @lang('admin::app.customers.addresses.create.phone')
                                                </x-admin::form.control-group.label>

                                                <x-admin::form.control-group.control
                                                    type="text"
                                                    name="phone"
                                                    rules="required|integer"
                                                    :label="trans('admin::app.customers.addresses.create.phone')"
                                                    :placeholder="trans('admin::app.customers.addresses.create.phone')"
                                                >
                                                </x-admin::form.control-group.control>

                                                <x-admin::form.control-group.error
                                                    control-name="phone"
                                                >
                                                </x-admin::form.control-group.error>
                                            </x-admin::form.control-group>
                                        </div>
                                        
                                        <div class="w-full">
                                            <!-- Default Address -->
                                            <x-admin::form.control-group class="flex gap-[10px] mt-[20px]">
                                                <x-admin::form.control-group.control
                                                    type="checkbox"
                                                    name="default_address"
                                                    :value="1"
                                                    id="default_address"
                                                    :label="trans('admin::app.customers.addresses.create.default-address')"
                                                    :checked="false"
                                                >
                                                </x-admin::form.control-group.control>

                                                <x-admin::form.control-group.label 
                                                    for="default_address"
                                                    class="text-gray-600 font-semibold cursor-pointer" 
                                                >
                                                    @lang('admin::app.customers.addresses.create.default-address')
                                                </x-admin::form.control-group.label>

                                                <x-admin::form.control-group.error
                                                    control-name="default_address"
                                                >
                                                </x-admin::form.control-group.error>
                                            </x-admin::form.control-group>
                                        </div>
                                    </div>
                                </div>

                                {!! view_render_event('bagisto.admin.customers.create.after') !!}
                            </x-slot:content>
            
                            <x-slot:footer>
                                <!-- Modal Submission -->
                                <div class="flex gap-x-[10px] items-center">
                                    <button 
                                        type="submit"
                                        class="px-[12px] py-[6px] bg-blue-600 border border-blue-700 rounded-[6px] text-gray-50 font-semibold cursor-pointer"
                                    >
                                        @lang('admin::app.customers.addresses.create.save-btn-title') 
                                    </button>
                                </div>
                            </x-slot:footer>
                        </x-admin::modal>
                    </form>
                </x-admin::form>
            </div>
        </script>

        <script type="module">
            app.component('v-create-customer-address', {
                template: '#v-create-customer-address-template',

                props: {
                    addressLines: {
                        type: Number,
                        default: 0, // Default to 0 if no data is provided
                    }
                },

                methods: {

                    create(params, { resetForm, setErrors }) {
                        this.$axios.post('{{ route("admin.customer.addresses.store", $customer->id) }}', params,
                            {
                                headers: {
                                'Content-Type': 'multipart/form-data'
                                }
                            }
                            )
                        
                            .then((response) => {
                                this.$refs.CustomerAddress.toggle();
                                
                                window.location.reload();

                                resetForm();
                            })
                            .catch(error => {
                                if (error.response.status ==422) {
                                    setErrors(error.response.data.errors);
                                }
                            });
                    }
                }
            })
        </script>
    @endPushOnce