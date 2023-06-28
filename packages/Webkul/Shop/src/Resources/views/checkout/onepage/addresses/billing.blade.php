<div>
    <div v-if="! forms.billing.isNew">
        <x-shop::accordion class="!border-b-[0px]">
            <x-slot:header >
                <div class="flex justify-between items-center">
                    <h2 class="text-[26px] font-medium max-sm:text-[20px]">
                        @lang('Billing Address')
                    </h2>
                </div>
            </x-slot:header>
        
            <x-slot:content>
                <div class="grid mt-[30px] gap-[20px] grid-cols-2 max-1060:grid-cols-[1fr] max-lg:grid-cols-2 max-sm:grid-cols-1 max-sm:mt-[15px]">
                    <div 
                        class="border border-[#e5e5e5] max-w-[414px] rounded-[12px] p-[0px] max-sm:flex-wrap relative select-none cursor-pointer"
                        v-for="(address, index) in addresses"
                    >
                        <input
                            type="radio"
                            name="billing[address_id]"
                            :id="'billing_address_id_' + address.id"
                            :value="address.id"
                            rules="required"
                            v-model="forms.billing.address.address_id"
                            class="hidden peer"
                            @change="resetPaymentAndShippingMethod"
                            :checked="address.isDefault"
                        >

                        <label 
                            class="icon-radio-unselect text-[24px] text-navyBlue absolute right-[20px] top-[20px] peer-checked:icon-radio-select cursor-pointer"
                            :for="'billing_address_id_' + address.id"
                        >
                        </label>

                        <label 
                            :for="'billing_address_id_' + address.id"
                            class="block p-[20px] rounded-[12px] cursor-pointer"
                        >
                            <div class="flex justify-between items-center">
                                <p class="text-[16px] font-medium">
                                    @{{ address.first_name }} @{{ address.last_name }}
                                    <span v-if="address.company_name">(@{{ address.company_name }})</span>
                                </p>
                            </div>

                            <p class="text-[#7D7D7D] mt-[25px] text-[14px] text-[14px]">
                                <template v-if="typeof address.address1 === 'string'">
                                    @{{ address.address1 }}
                                </template>

                                <template v-else>
                                    @{{ address.address1.join(', ') }}
                                </template>
                                
                                @{{ address.city }}, 
                                @{{ address.state }}, @{{ address.country }}, 
                                @{{ address.postcode }}
                            </p>
                        </label>
                    </div>

                    <div 
                        class="flex justify-center items-center border border-[#e5e5e5] rounded-[12px] p-[20px] max-w-[414px] max-sm:flex-wrap"
                        @click="showNewBillingAddressForm"
                    >
                        <div class="flex gap-x-[10px] items-center cursor-pointer">
                            <span class="icon-plus text-[30px] p-[10px] border border-black rounded-full"></span>
                            <p class="text-[16px]">@lang('Add new address')</p>
                        </div>
                    </div>
                </div>

                <div class="select-none mt-[20px] text-[14px] text-[#7D7D7D] flex gap-x-[15px]">
                    <input
                        type="checkbox"
                        id="isUsedForShipping"
                        name="is_use_for_shipping"
                        class="hidden peer"
                        v-model="forms.billing.isUsedForShipping"
                    />
            
                    <label 
                        class="icon-uncheck text-[20px] text-navyBlue peer-checked:icon-check peer-checked:bg-navyBlue peer-checked:rounded-[4px] peer-checked:text-white cursor-pointer"
                        for="isUsedForShipping"
                    >
                    </label>
                    
                    <label 
                        for="isUsedForShipping"
                        class="cursor-pointer"
                    >
                        @lang('address is the same as my billing address')
                    </label>
                </div>

                <div v-if="! forms.billing.isNew && ! forms.shipping.isNew && forms.billing.isUsedForShipping">
                    <div class="flex justify-end mt-4 mb-4">
                        <button
                            class="block bg-navyBlue text-white text-base w-max font-medium py-[11px] px-[43px] rounded-[18px] text-center cursor-pointer"
                            @click="store"
                        >
                            @lang('Confirm')
                        </button>
                    </div>
                </div>
            </x-slot:content>
        </x-shop::accordion>
    </div>

    <div v-else>
        <x-shop::accordion>
            <x-slot:header>
                <div class="flex justify-between items-center">
                    <h2 class="text-[26px] font-medium max-sm:text-[20px]">
                        @lang('Billing Address')
                    </h2>
                </div>
            </x-slot:header>
        
            <x-slot:content>
                <div>
                    <a 
                        class="flex justify-end"
                        href="javascript:void(0)" 
                        v-if="addresses.length > 0"
                        @click="forms.billing.isNew = ! forms.billing.isNew"
                    >
                        <span class="icon-arrow-left text-[24px]"></span>

                        <span>@lang('Back')</span>
                    </a>
                </div>

                <x-shop::form
                    v-slot="{ meta, errors, handleSubmit }"
                    as="div"
                >
                    <form @submit="handleSubmit($event, handleBillingAddressForm)">
                        <x-shop::form.control-group>
                            <x-shop::form.control-group.label>
                                @lang('Company name')
                            </x-shop::form.control-group.label>
                

                            <x-shop::form.control-group.control
                                type="text"
                                name="billing[company_name]"
                                label="Company name"
                                placeholder="Company name"
                                v-model="forms.billing.address.company_name"
                            >
                            </x-shop::form.control-group.control>
    
                            <x-shop::form.control-group.error
                                control-name="billing[company_name]"
                            >
                            </x-shop::form.control-group.error>
                        </x-shop::form.control-group>
    

                        <div class="grid grid-cols-2 gap-x-[20px]">
                            <x-shop::form.control-group>
                                <x-shop::form.control-group.label class="!mt-[0px]">
                                    @lang('First name')
                                </x-shop::form.control-group.label>
        
                                <x-shop::form.control-group.control
                                    type="text"
                                    name="billing[first_name]"
                                    label="First name"
                                    rules="required"
                                    placeholder="First name"
                                    v-model="forms.billing.address.first_name"
                                >
                                </x-shop::form.control-group.control>
        
                                <x-shop::form.control-group.error
                                    control-name="billing[first_name]"
                                >
                                </x-shop::form.control-group.error>
                            </x-shop::form.control-group>

                            <x-shop::form.control-group>
                                <x-shop::form.control-group.label class="!mt-[0px]">
                                    @lang('Last name')
                                </x-shop::form.control-group.label>
        
                                <x-shop::form.control-group.control
                                    type="text"
                                    name="billing[last_name]"
                                    label="Last name"
                                    rules="required"
                                    placeholder="Last name"
                                    v-model="forms.billing.address.last_name"
                                >
                                </x-shop::form.control-group.control>
        
                                <x-shop::form.control-group.error
                                    control-name="billing[last_name]"
                                >
                                </x-shop::form.control-group.error>
                            </x-shop::form.control-group>
                        </div>
    
                        <x-shop::form.control-group>
                            <x-shop::form.control-group.label class="!mt-[0px]">
                                @lang('Email')
                            </x-shop::form.control-group.label>
    
                            <x-shop::form.control-group.control
                                type="email"
                                name="billing[email]"
                                rules="required|email"
                                label="Email"
                                placeholder="email@example.com"
                                v-model="forms.billing.address.email"
                            >
                            </x-shop::form.control-group.control>
    
                            <x-shop::form.control-group.error
                                control-name="billing[email]"
                            >
                            </x-shop::form.control-group.error>
                        </x-shop::form.control-group>
    
                        <x-shop::form.control-group>
                            <x-shop::form.control-group.label class="!mt-[0px]">
                                @lang('Street address')
                            </x-shop::form.control-group.label>
    
                            <x-shop::form.control-group.control
                                type="text"
                                name="billing[address1][]"
                                class="text-[14px] shadow appearance-none border rounded w-full py-2 px-3 focus:outline-none focus:shadow-outline"
                                rules="required"
                                label="Street address"
                                placeholder="Street address"
                                v-model="forms.billing.address.address1[0]"
                            >
                            </x-shop::form.control-group.control>

                            <x-shop::form.control-group.error
                                class="mb-2"
                                control-name="billing[address1][]"
                            >
                            </x-shop::form.control-group.error>

                            @if (core()->getConfigData('customer.address.information.street_lines') > 1)
                                @for ($i = 1; $i < core()->getConfigData('customer.address.information.street_lines'); $i++)
                                    <x-shop::form.control-group.control
                                        type="text"
                                        name="billing[address1][{{ $i }}]"
                                        class="text-[14px] shadow appearance-none border rounded w-full py-2 px-3 focus:outline-none focus:shadow-outline"
                                        label="Street address"
                                        placeholder="Street address"
                                        v-model="forms.billing.address.address1[{{$i}}]"
                                    >
                                    </x-shop::form.control-group.control>
                                @endfor
                            @endif
                        </x-shop::form.control-group>
    

                        <div class="grid grid-cols-2 gap-x-[20px]">
                            <x-shop::form.control-group
                                class="!mb-4"
                            >
                                <x-shop::form.control-group.label class="!mt-[0px]">
                                    @lang('Country')
                                </x-shop::form.control-group.label>
        
                                <x-shop::form.control-group.control
                                    type="select"
                                    name="billing[country]"
                                    class="!text-[14px] bg-white border rounded w-full py-2 px-3 focus:outline-none focus:shadow-outline mb-2"
                                    rules="required"
                                    label="Country"
                                    placeholder="Country"
                                    v-model="forms.billing.address.country"
                                >
                                    @foreach (core()->countries() as $country)
                                        <option value="{{ $country->code }}">{{ $country->name }}</option>
                                    @endforeach
                                </x-shop::form.control-group.control>
        
                                <x-shop::form.control-group.error
                                    control-name="billing[country]"
                                >
                                </x-shop::form.control-group.error>
                            </x-shop::form.control-group>
    
                            <x-shop::form.control-group>
                                <x-shop::form.control-group.label class="!mt-[0px]">
                                    @lang('State')
                                </x-shop::form.control-group.label>
        
                                <x-shop::form.control-group.control
                                    type="text"
                                    name="billing[state]"
                                    class="text-[14px] bg-white border rounded w-full py-2 px-3 focus:outline-none focus:shadow-outline"
                                    rules="required"
                                    label="State"
                                    placeholder="State"
                                    v-model="forms.billing.address.state"
                                    v-if="! haveStates('billing')"
                                >
                                </x-shop::form.control-group.control>

                                <x-shop::form.control-group.control
                                    type="select"
                                    name="billing[state]"
                                    class="text-[14px] bg-white border rounded w-full py-2 px-3 focus:outline-none focus:shadow-outline mb-2"
                                    rules="required"
                                    label="State"
                                    placeholder="State"
                                    v-model="forms.billing.address.state"
                                    v-if="haveStates('billing')"
                                >
                                    <option value="">@lang('Select state')</option>

                                    <option 
                                        v-for='(state, index) in states[forms.billing.address.country]' 
                                        :value="state.code" 
                                    >
                                        @{{ state.default_name }}
                                    </option>
                                </x-shop::form.control-group.control>
        
                                <x-shop::form.control-group.error
                                    control-name="billing[state]"
                                >
                                </x-shop::form.control-group.error>
                            </x-shop::form.control-group>
                        </div>
    
                        <div class="grid grid-cols-2 gap-x-[20px]">
                            <x-shop::form.control-group>
                                <x-shop::form.control-group.label class="!mt-[0px]">
                                    @lang('City')
                                </x-shop::form.control-group.label>
    
                                <x-shop::form.control-group.control
                                    type="text"
                                    name="billing[city]"
                                    class="text-[14px] bg-white border rounded w-full py-2 px-3 focus:outline-none focus:shadow-outline"
                                    rules="required"
                                    label="City"
                                    placeholder="City"
                                    v-model="forms.billing.address.city"
                                >
                                </x-shop::form.control-group.control>
    
                                <x-shop::form.control-group.error
                                    control-name="billing[city]"
                                >
                                </x-shop::form.control-group.error>
                            </x-shop::form.control-group>
        
                            <x-shop::form.control-group>
                                <x-shop::form.control-group.label class="!mt-[0px]">
                                    @lang('Zip/Postcode')
                                </x-shop::form.control-group.label>
        
                                <x-shop::form.control-group.control
                                    type="text"
                                    name="billing[postcode]"
                                    class="text-[14px] bg-white border rounded w-full py-2 px-3 focus:outline-none focus:shadow-outline"
                                    rules="required"
                                    label="Zip/Postcode"
                                    placeholder="Zip/Postcode"
                                    v-model="forms.billing.address.postcode"
                                >
                                </x-shop::form.control-group.control>
        
                                <x-shop::form.control-group.error
                                    control-name="billing[postcode]"
                                >
                                </x-shop::form.control-group.error>
                            </x-shop::form.control-group>
                        </div>

                        <x-shop::form.control-group>
                            <x-shop::form.control-group.label class="!mt-[0px]">
                                @lang('Telephone')
                            </x-shop::form.control-group.label>
    
                            
                            <x-shop::form.control-group.control
                                type="text"
                                name="billing[phone]"
                                class="text-[14px] bg-white border rounded w-full py-2 px-3 focus:outline-none focus:shadow-outline"
                                rules="required|numeric"
                                label="Telephone"
                                placeholder="Telephone"
                                v-model="forms.billing.address.phone"
                            >
                            </x-shop::form.control-group.control>
    
                            <x-shop::form.control-group.error
                                control-name="billing[phone]"
                            >
                            </x-shop::form.control-group.error>
                        </x-shop::form.control-group>

                        <div class="mt-[30px] pb-[15px]">
                            <div class="grid gap-[10px]">
                                @auth('customer')
                                    <div class="select-none flex gap-x-[15px]">
                                        <input 
                                            type="checkbox"
                                            name="billing[is_save_as_address]"
                                            id="billing[is_save_as_address]"
                                            class="hidden peer"
                                            v-model="forms.billing.address.isSaved"
                                        >

                                        <label
                                            class="icon-uncheck text-[24px] text-navyBlue peer-checked:icon-check peer-checked:bg-navyBlue peer-checked:rounded-[4px] peer-checked:text-white  cursor-pointer"
                                            for="billing[is_save_as_address]"
                                        ></label>

                                        <label for="billing[is_save_as_address]">@lang('Save this address')</label>
                                    </div>
                                @endauth
                            </div>
                        </div>

                        <div class="flex justify-end mt-4 mb-4">
                            <button
                                type="submit"
                                class="block bg-navyBlue text-white text-base w-max font-medium py-[11px] px-[43px] rounded-[18px] text-center cursor-pointer"
                            >
                                @lang('Confirm')
                            </button>
                        </div>
                    </form>
                </x-shop::form>
            </x-slot:content>
        </x-shop::accordion>
    </div>
</div>
