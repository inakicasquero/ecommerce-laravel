<x-admin::layouts>
    {{-- Title of the page --}}
    <x-slot:title>
        @lang('admin::app.customers.index.title')
    </x-slot:title>

    <div class="flex justify-between items-center">
        <p class="text-[20px] text-gray-800 font-bold">
            @lang('admin::app.customers.index.title')
        </p>

        <div class="flex gap-x-[10px] items-center">
            <!-- Dropdown -->
            <x-admin::dropdown position="bottom-right">
                <x-slot:toggle>
                    <span class="flex icon-setting p-[6px] rounded-[6px] text-[24px]  cursor-pointer transition-all hover:bg-gray-200"></span>
                </x-slot:toggle>

                <x-slot:content class="w-[174px] max-w-full !p-[8PX] border border-gray-300 rounded-[4px] z-10 bg-white shadow-[0px_8px_10px_0px_rgba(0,_0,_0,_0.2)]">
                    <div class="grid gap-[2px]">
                        <div class="p-[6px] items-center cursor-pointer transition-all hover:bg-gray-100 hover:rounded-[6px]">
                            <!-- Export Modal -->
                            <x-admin::modal ref="exportModal">
                                <x-slot:toggle>
                                    <p class="text-gray-600 font-semibold leading-[24px]">
                                        Export                                            
                                    </p>
                                </x-slot:toggle>

                                <x-slot:header>
                                    <p class="text-[18px] text-gray-800 font-bold">
                                        @lang('Download')
                                    </p>
                                </x-slot:header>

                                <x-slot:content>
                                    <div class="p-[16px]">
                                        <x-admin::form action="">
                                            <x-admin::form.control-group>
                                                <x-admin::form.control-group.control
                                                    type="select"
                                                    name="format"
                                                    id="format"
                                                >
                                                    <option value="xls">XLS</option>
                                                    <option value="csv">CLS</option>
                                                </x-admin::form.control-group.control>
                                            </x-admin::form.control-group>
                                        </x-admin::form>
                                    </div>
                                </x-slot:content>
                                <x-slot:footer>
                                    <!-- Save Button -->
                                    <button
                                        type="submit" 
                                        class="primary-button"
                                    >
                                        @lang('Export')
                                    </button>
                                </x-slot:footer>
                            </x-admin::modal>
                        </div>
                    </div>
                </x-slot:content>
            </x-admin::dropdown>

            <div class="flex gap-x-[10px] items-center">
                {{-- Customer Create Vue Component --}}
                <v-create-customer-form>
                    <button
                        type="button"
                        class="primary-button"
                    >
                        @lang('admin::app.customers.index.create.create-btn')
                    </button>
                </v-create-customer-form>
            </div>
        </div>
    </div>

    <x-admin::datagrid src="{{ route('admin.customers.customers.index') }}" ref="customer_data" :isMultiRow="true">
        {{-- Datagrid Header --}}
        <template #header="{ columns, records, sortPage, selectAllRecords, applied, isLoading}">
            <template v-if="! isLoading">
                <div class="row grid grid-cols-[2fr_1fr_1fr] grid-rows-1 items-center px-[16px] py-[10px] border-b-[1px] border-gray-300">
                    <div
                        class="flex gap-[10px] items-center select-none"
                        v-for="(columnGroup, index) in [['full_name', 'email', 'phone'], ['status', 'gender', 'group'], ['total_base_grand_total', 'order_count', 'address_count']]"
                    >
                        <label 
                            class="flex gap-[4px] items-center w-max cursor-pointer select-none"
                            for="mass_action_select_all_records"
                            v-if="! index"
                        >
                            <input 
                                type="checkbox" 
                                name="mass_action_select_all_records"
                                id="mass_action_select_all_records"
                                class="hidden peer"
                                :checked="['all', 'partial'].includes(applied.massActions.meta.mode)"
                                @change="selectAllRecords"
                            >
                
                            <span
                                class="icon-uncheckbox cursor-pointer rounded-[6px] text-[24px]"
                                :class="[
                                    applied.massActions.meta.mode === 'all' ? 'peer-checked:icon-checked peer-checked:text-blue-600' : (
                                        applied.massActions.meta.mode === 'partial' ? 'peer-checked:icon-checkbox-partial peer-checked:text-blue-600' : ''
                                    ),
                                ]"
                            >
                            </span>
                        </label>

                        <p class="text-gray-600">
                            <span class="[&>*]:after:content-['_/_']">
                                <template v-for="column in columnGroup">
                                    <span
                                        class="after:content-['/'] last:after:content-['']"
                                        :class="{
                                            'text-gray-800 font-medium': applied.sort.column == column,
                                            'cursor-pointer hover:text-gray-800': columns.find(columnTemp => columnTemp.index === column)?.sortable,
                                        }"
                                        @click="
                                            columns.find(columnTemp => columnTemp.index === column)?.sortable ? sortPage(columns.find(columnTemp => columnTemp.index === column)): {}
                                        "
                                    >
                                        @{{ columns.find(columnTemp => columnTemp.index === column)?.label }}
                                    </span>
                                </template>
                            </span>

                            <i
                                class="ml-[5px] text-[16px] text-gray-800 align-text-bottom"
                                :class="[applied.sort.order === 'asc' ? 'icon-down-stat': 'icon-up-stat']"
                                v-if="columnGroup.includes(applied.sort.column)"
                            ></i>
                        </p>
                    </div>
                </div>
            </template>

            {{-- Datagrid Head Shimmer --}}
            <template v-else>
                <x-admin::shimmer.datagrid.table.head :isMultiRow="true"></x-admin::shimmer.datagrid.table.head>
            </template>
        </template>

        {{-- Datagrid Body --}}
        <template #body="{ columns, records, setCurrentSelectionMode, applied, isLoading }">
            <template v-if="! isLoading">
                <div
                    class="row grid grid-cols-[minmax(150px,_2fr)_1fr_1fr] px-[16px] py-[10px] border-b-[1px] border-gray-300 transition-all hover:bg-gray-100"
                    v-for="record in records"
                >
                    <div class="flex gap-[10px]">
                        <input 
                            type="checkbox" 
                            :name="`mass_action_select_record_${record.customer_id}`"
                            :id="`mass_action_select_record_${record.customer_id}`"
                            :value="record.customer_id"
                            class="hidden peer"
                            v-model="applied.massActions.indices"
                            @change="setCurrentSelectionMode"
                        >

                        <label 
                            class="icon-uncheckbox rounded-[6px] text-[24px] cursor-pointer peer-checked:icon-checked peer-checked:text-blue-600"
                            :for="`mass_action_select_record_${record.customer_id}`"
                        >
                        </label>
                        <div class="flex flex-col gap-[6px]">
                            <p 
                                class="text-[16px] text-gray-800 font-semibold" 
                                v-text="record.full_name"
                            >
                            </p>

                            <p 
                                class="text-gray-600" 
                                v-text="record.email"
                            >
                            </p>

                            <p 
                                class="text-gray-600"
                                v-text="record.phone ?? 'N/A'"
                            >
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-col gap-[6px]">
                        <div class="flex gap-[6px]">
                            <span
                                :class="{
                                    'label-cancelled': record.status == '',
                                    'label-active': record.status === 1,
                                }"
                            >
                                @{{ record.status ? 'Active' : 'Inactive' }}
                            </span>

                            <span
                                :class="{
                                    'label-cancelled': record.is_suspended === 1,
                                }"
                            >
                                @{{ record.is_suspended ?  'Suspended' : '' }}
                            </span>
                        </div>

                        <p 
                            class="text-gray-600"
                            v-text="record.gender ?? 'N/A'"
                        >
                        </p>

                        <p 
                            class="text-gray-600"
                            v-text="record.group ?? 'N/A'"
                        >
                        </p>
                    </div>

                    <div class="flex gap-x-[16px] justify-between items-center">
                        <div class="flex flex-col gap-[6px]">
                            <p 
                                class="text-[16px] text-gray-800 font-semibold" 
                                v-text="$admin.formatPrice(record.total_base_grand_total)"
                            >
                            </p>
                            
                            <p class="text-gray-600">
                                @{{ "@lang('admin::app.customers.index.datagrid.order')".replace(':order', record.order_count) }}
                            </p>

                            <p class="text-gray-600">
                                @{{ "@lang('admin::app.customers.index.datagrid.address')".replace(':address', record.address_count) }}
                            </p>
                        </div>
                        <div class="flex items-center">
                            <a 
                                class="icon-login text-[24px] ml-[4px] p-[6px] cursor-pointer hover:bg-gray-200 hover:rounded-[6px]"
                                :href=`{{ route('admin.customers.customers.login_as_customer', '') }}/${record.customer_id}`
                            >
                            </a>

                            <a 
                                class="icon-sort-right text-[24px] ml-[4px] p-[6px] cursor-pointer hover:bg-gray-200 hover:rounded-[6px]"
                                :href=`{{ route('admin.customers.customers.view', '') }}/${record.customer_id}`
                            >
                            </a>
                        </div>    
                    </div>
                </div>
            </template>

            {{-- Datagrid Body Shimmer --}}
            <template v-else>
                <x-admin::shimmer.datagrid.table.body :isMultiRow="true"></x-admin::shimmer.datagrid.table.body>
            </template>
        </template>
    </x-admin::datagrid>

    @pushOnce('scripts')
        <script type="text/x-template" id="v-create-customer-form-template">
            <div>
                <!-- Create Button -->
                @if (bouncer()->hasPermission('customers.customers.create'))
                    <button
                        type="button"
                        class="primary-button"
                        @click="$refs.customerCreateModal.toggle()"
                    >
                        @lang('admin::app.customers.index.create.create-btn')
                    </button>
                @endif

                <x-admin::form
                    v-slot="{ meta, errors, handleSubmit }"
                    as="div"
                >
                    <form @submit="handleSubmit($event, create)">
                        <!-- Customer Create Modal -->
                        <x-admin::modal ref="customerCreateModal">
                            <x-slot:header>
                                <!-- Modal Header -->
                                <p class="text-[18px] text-gray-800 font-bold">
                                    @lang('admin::app.customers.index.create.title')
                                </p>    
                            </x-slot:header>
            
                            <x-slot:content>
                                <!-- Modal Content -->
                                {!! view_render_event('bagisto.admin.customers.create.before') !!}

                                <div class="px-[16px] py-[10px] border-b-[1px] border-gray-300">
                                    <div class="flex gap-[16px] max-sm:flex-wrap">
                                        <!-- First Name -->
                                        <x-admin::form.control-group class="w-full mb-[10px]">
                                            <x-admin::form.control-group.label>
                                                @lang('admin::app.customers.index.create.first-name')
                                            </x-admin::form.control-group.label>
                
                                            <x-admin::form.control-group.control
                                                type="text"
                                                name="first_name" 
                                                id="first_name" 
                                                rules="required"
                                                :label="trans('admin::app.customers.index.create.first-name')"
                                                :placeholder="trans('admin::app.customers.index.create.first-name')"
                                            >
                                            </x-admin::form.control-group.control>
                
                                            <x-admin::form.control-group.error
                                                control-name="first_name"
                                            >
                                            </x-admin::form.control-group.error>
                                        </x-admin::form.control-group>
        
                                        <!-- Last Name -->
                                        <x-admin::form.control-group class="w-full mb-[10px]">
                                            <x-admin::form.control-group.label>
                                                @lang('admin::app.customers.index.create.last-name')
                                            </x-admin::form.control-group.label>
                
                                            <x-admin::form.control-group.control
                                                type="text"
                                                name="last_name" 
                                                id="last_name"
                                                rules="required"
                                                :label="trans('admin::app.customers.index.create.last-name')"
                                                :placeholder="trans('admin::app.customers.index.create.last-name')"
                                            >
                                            </x-admin::form.control-group.control>
                
                                            <x-admin::form.control-group.error
                                                control-name="last_name"
                                            >
                                            </x-admin::form.control-group.error>
                                        </x-admin::form.control-group>
                                    </div>

                                    <!-- Email -->
                                    <x-admin::form.control-group class="mb-[10px]">
                                        <x-admin::form.control-group.label>
                                            @lang('admin::app.customers.index.create.email')
                                        </x-admin::form.control-group.label>
            
                                        <x-admin::form.control-group.control
                                            type="email"
                                            name="email"
                                            id="email"
                                            rules="required|email"
                                            :label="trans('admin::app.customers.index.create.email')"
                                            placeholder="email@example.com"
                                        >
                                        </x-admin::form.control-group.control>
            
                                        <x-admin::form.control-group.error
                                            control-name="email"
                                        >
                                        </x-admin::form.control-group.error>
                                    </x-admin::form.control-group>

                                    <!-- Contact Number -->
                                    <x-admin::form.control-group class="mb-[10px]">
                                        <x-admin::form.control-group.label>
                                            @lang('admin::app.customers.index.create.contact-number')
                                        </x-admin::form.control-group.label>
            
                                        <x-admin::form.control-group.control
                                            type="text"
                                            name="phone"
                                            id="phone"
                                            rules="required|integer"
                                            :label="trans('admin::app.customers.index.create.contact-number')"
                                            :placeholder="trans('admin::app.customers.index.create.contact-number')"
                                        >
                                        </x-admin::form.control-group.control>
            
                                        <x-admin::form.control-group.error
                                            control-name="phone"
                                        >
                                        </x-admin::form.control-group.error>
                                    </x-admin::form.control-group>
            
                                    <x-admin::form.control-group class="mb-[10px]">
                                        <x-admin::form.control-group.label>
                                            @lang('admin::app.customers.index.create.date-of-birth')
                                        </x-admin::form.control-group.label>
            
                                        <x-admin::form.control-group.control
                                            type="date"
                                            name="date_of_birth" 
                                            id="dob"
                                            :label="trans('admin::app.customers.index.create.date-of-birth')"
                                            :placeholder="trans('admin::app.customers.index.create.date-of-birth')"
                                        >
                                        </x-admin::form.control-group.control>
            
                                        <x-admin::form.control-group.error
                                            control-name="date_of_birth"
                                        >
                                        </x-admin::form.control-group.error>
                                    </x-admin::form.control-group>
            
                                    <div class="flex gap-[16px] max-sm:flex-wrap">
                                        <!-- Gender -->
                                        <x-admin::form.control-group class="w-full">
                                            <x-admin::form.control-group.label>
                                                @lang('admin::app.customers.index.create.gender')
                                            </x-admin::form.control-group.label>
                
                                            <x-admin::form.control-group.control
                                                type="select"
                                                name="gender"
                                                id="gender"
                                                rules="required"
                                                :label="trans('admin::app.customers.index.create.gender')"
                                            >
                                                <option value="Male">
                                                    @lang('admin::app.customers.index.create.male')
                                                </option>
                                                
                                                <option value="Female">
                                                    @lang('admin::app.customers.index.create.female')
                                                </option>

                                                <option value="Other">
                                                    @lang('admin::app.customers.index.create.other')
                                                </option>
                                            </x-admin::form.control-group.control>
                
                                            <x-admin::form.control-group.error
                                                control-name="gender"
                                            >
                                            </x-admin::form.control-group.error>
                                        </x-admin::form.control-group>

                                        <!-- Customer Group -->
                                        <x-admin::form.control-group class="w-full">
                                            <x-admin::form.control-group.label>
                                                @lang('admin::app.customers.index.create.customer-group')
                                            </x-admin::form.control-group.label>
                
                                            <x-admin::form.control-group.control
                                                type="select"
                                                name="customer_group_id"
                                                id="customerGroup"
                                                :label="trans('admin::app.customers.index.create.customer-group')"
                                            >
                                                @foreach ($groups as $group)
                                                    <option value="{{ $group->id }}"> {{ $group->name}} </option>
                                                @endforeach
                                            </x-admin::form.control-group.control>
                
                                            <x-admin::form.control-group.error
                                                control-name="customer_group_id"
                                            >
                                            </x-admin::form.control-group.error>
                                        </x-admin::form.control-group>
                                    </div>

                                    {!! view_render_event('bagisto.admin.customers.create.after') !!}

                                </div>
                            </x-slot:content>
            
                            <x-slot:footer>
                                <!-- Modal Submission -->
                                <div class="flex gap-x-[10px] items-center">
                                    <!-- Save Button -->
                                    <button 
                                        type="submit"
                                        class="primary-button"
                                    >
                                        @lang('admin::app.customers.index.create.save-btn')
                                    </button>
                                </div>
                            </x-slot:footer>
                        </x-admin::modal>
                    </form>
                </x-admin::form>
            </div>
        </script>

        <script type="module">
            app.component('v-create-customer-form', {
                template: '#v-create-customer-form-template',

                methods: {
                    create(params, { resetForm, setErrors }) {
                    
                        this.$axios.post("{{ route('admin.customers.customers.store') }}", params)
                            .then((response) => {
                                this.$refs.customerCreateModal.close();

                                this.$root.$refs.customer_data.get();

                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.data.message });

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
</x-admin::layouts>