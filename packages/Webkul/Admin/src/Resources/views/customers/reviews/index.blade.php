<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.customers.reviews.index.title')
    </x-slot:title>

    <div class="flex  gap-[16px] justify-between items-center max-sm:flex-wrap">
        <p class="py-[11px] text-[20px] text-gray-800 font-bold">
            @lang('admin::app.customers.reviews.index.title')
        </p>
    </div>

    <v-review-edit-drawer></v-review-edit-drawer>

    @pushOnce('scripts')
        <script type="text/x-template" id="v-review-edit-drawer-template">
            <x-admin::datagrid
                src="{{ route('admin.customer.review.index') }}"
                :isMultiRow="true"
                ref="review_data"
            >
                {{-- Datagrid Header --}}
                <template #header="{ columns, records, sortPage, selectAllRecords, applied, isLoading }">
                    <template v-if="! isLoading">
                        <div class="row grid grid-rows-1 grid-cols-[2fr_1fr_minmax(150px,_4fr)_0.5fr] items-center px-[16px] py-[10px] border-b-[1px] border-gray-300">
                            <div
                                class="flex gap-[10px] items-center"
                                v-for="(columnGroup, index) in [['name', 'product_name', 'product_review_status'], ['rating', 'created_at', 'product_review_id'], ['title', 'comment']]"
                            >
                                <label
                                    class="flex gap-[4px] w-max items-center cursor-pointer select-none"
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
                                                applied.massActions.meta.mode === 'partial' ? 'peer-checked:icon-checkbox-partial peer-checked:text-navyBlue' : ''
                                            ),
                                        ]"
                                    >
                                    </span>
                                </label>

                                {{-- Product Name, Review Status --}}
                                <p class="text-gray-600">
                                    <span class="[&>*]:after:content-['_/_']">
                                        <template v-for="column in columnGroup">
                                            <span
                                                class="after:content-['/'] last:after:content-['']"
                                                :class="{
                                                    'text-gray-800 font-medium': applied.sort.column == column,
                                                    'cursor-pointer': columns.find(columnTemp => columnTemp.index === column)?.sortable,
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

                <template #body="{ columns, records, setCurrentSelectionMode, applied, isLoading }">
                    <template v-if="! isLoading">
                        <div
                            class="row grid grid-cols-[2fr_1fr_minmax(150px,_4fr)_0.5fr] px-[16px] py-[10px] border-b-[1px] border-gray-300"
                            v-for="record in records"
                        >
                            {{-- Name, Product, Description --}}
                            <div class="flex gap-[10px]">
                                <input 
                                    type="checkbox" 
                                    :name="`mass_action_select_record_${record.product_review_id}`"
                                    :id="`mass_action_select_record_${record.product_review_id}`"
                                    :value="record.product_review_id"
                                    class="hidden peer"
                                    v-model="applied.massActions.indices"
                                    @change="setCurrentSelectionMode"
                                >
                    
                                <label 
                                    class="icon-uncheckbox rounded-[6px] text-[24px] cursor-pointer peer-checked:icon-checked peer-checked:text-blue-600"
                                    :for="`mass_action_select_record_${record.product_review_id}`"
                                ></label>

                                <div class="flex flex-col gap-[6px]">
                                    <p
                                        class="text-[16px] text-gray-800 font-semibold"
                                        v-text="record.customer_full_name"
                                    >
                                    </p>
                                    <p
                                        class="text-gray-600"
                                        v-text="record.product_name"
                                    >
                                    </p>

                                    <p
                                        :class="{
                                            'label-cancelled': record.product_review_status === 'disapproved',
                                            'label-pending': record.product_review_status === 'pending',
                                            'label-active': record.product_review_status === 'approved',
                                        }"
                                        v-text="record.product_review_status"
                                    >
                                    </p>
                                </div>
                            </div>

                            {{-- Rating, Date, Id Section --}}
                            <div class="flex flex-col gap-[6px]">
                                <div class="flex">
                                    <x-admin::star-rating 
                                        :is-editable="false"
                                        ::value="record.rating"
                                    >
                                    </x-admin::star-rating>
                                </div>

                                <p
                                    class="text-gray-600"
                                    v-text="record.created_at"
                                >
                                </p>

                                <p
                                    class="text-gray-600"
                                >
                                    @{{ "@lang('admin::app.customers.reviews.index.datagrid.review-id')".replace(':review-id', record.product_review_id) }}
                                </p>
                            </div>

                            {{-- Title, Description --}}
                            <div class="flex flex-col gap-[6px]">
                                <p
                                    class="text-[16px] text-gray-800 font-semibold"
                                    v-text="record.title"
                                >
                                </p>

                                <p
                                    class="text-gray-600"
                                    v-text="record.comment"
                                >
                                </p>
                            </div>

                            <div class="flex gap-[5px] place-content-end self-center">
                                <!-- Review Delete Button -->
                                <a :href=`{{ route('admin.customer.review.delete', '') }}/${record.product_review_id}`>
                                    <span class="icon-delete text-[24px] ml-[4px] p-[6px] rounded-[6px] cursor-pointer transition-all hover:bg-gray-100"></span>
                                </a>

                                <!-- View Button -->
                                    <span @click="edit(record.product_review_id)" class="icon-sort-right text-[24px] ml-[4px] p-[6px] rounded-[6px] cursor-pointer transition-all hover:bg-gray-100"></span>
                            </div>
                        </div>
                    </template>

                    {{-- Datagrid Body Shimmer --}}
                    <template v-else>
                        <x-admin::shimmer.datagrid.table.body :isMultiRow="true"></x-admin::shimmer.datagrid.table.body>
                    </template>
                </template>
            </x-admin::datagrid>

            <!-- Drawer content -->
            <div class="flex gap-[10px] mt-[14px] max-xl:flex-wrap">
                <!-- Left sub-component -->
                <div class=" flex flex-col gap-[8px] flex-1 max-xl:flex-auto">
                    <x-admin::form
                        v-slot="{ meta, errors, handleSubmit }"
                        as="div"
                    >
                        <form @submit="handleSubmit($event, update)">
                            <x-admin::drawer ref="review">
                                <!-- Drawer Header -->
                                <x-slot:header>
                                    <div class="flex justify-between items-center">
                                        <p class="text-[20px] font-medium">
                                            @lang('Edit Reivew')
                                        </p>
                    
                                        <button class="mr-[45px] px-[12px] py-[6px] bg-blue-600 border border-blue-700 rounded-[6px] text-gray-50 font-semibold cursor-pointer">
                                            @lang('admin::app.catalog.products.edit.types.configurable.edit.save-btn')
                                        </button>
                                    </div>
                                </x-slot:header>
                                <!-- Drawer Content -->
                    
                                <x-slot:content>
                                    <div class="flex flex-col gap-[16px] px-[5px] py-[10px]">
                                        <div class="grid grid-cols-2 gap-[16px]">
                                            <div class="">
                                                <p class="text-[12px] text-gray-800 font-semibold">Customer</p>
                                                <p class="text-gray-800 font-semibold" v-text="review.name !== '' ? review.name : 'N/A'"></p>
                                            </div>

                                            <div class="">
                                                <p class="text-[12px] text-gray-800 font-semibold">Product</p>
                                                <p class="text-gray-800 font-semibold" v-text="review.product.name"></p>
                                            </div>
                    
                                            <div class="">
                                                <p class="text-[12px] text-gray-800 font-semibold">ID</p>
                                                <p class="text-gray-800 font-semibold" v-text="review.id"></p>
                                            </div>
                    
                                            <div class="">
                                                <p class="text-[12px] text-gray-800 font-semibold">Date</p>
                                                <p class="text-gray-800 font-semibold" v-text="review.created_at"></p>
                                            </div>
                                        </div>
                                        <div class="w-full">
                                            <x-admin::form.control-group.control
                                                type="hidden"
                                                name="id"
                                                ::value="review.id"
                                                rules="required"
                                            >
                                            </x-admin::form.control-group.control>

                                            <x-admin::form.control-group>
                                                <x-admin::form.control-group.label class="required">
                                                    Status 
                                                </x-admin::form.control-group.label>
                                                <x-admin::form.control-group.control
                                                    type="select"
                                                    name="status"
                                                    ::value="review.status"
                                                    rules="required"
                                                >
                                                    <option value="approved" >
                                                        Approved
                                                    </option>
                    
                                                    <option value="disapproved">
                                                        Disapproved
                                                    </option>
                    
                                                    <option value="pending">
                                                        Pending
                                                    </option>
                                                </x-admin::form.control-group.control>
                    
                                                <x-admin::form.control-group.error
                                                    control-name="status"
                                                >
                                                </x-admin::form.control-group.error>
                                            </x-admin::form.control-group>
                                        </div>
                    
                                        <div class="w-full ">
                                            <label class="block text-[12px]  text-gray-800 font-medium leading-[24px]" for="username"> Rating </label>
                                            <div class="flex">
                                                <x-admin::star-rating 
                                                    :is-editable="false"
                                                    ::value="review.rating"
                                                >
                                                </x-admin::star-rating>
                                            </div>
                                        </div>
                    
                                        <div class="w-full">
                                            <label class="block text-[12px]  text-gray-800 font-medium leading-[24px]" for="title"> Title </label>
                                            <p class="text-gray-800 font-semibold" v-text="review.title"></p>
                                        </div>
                    
                                        <div class="w-full">
                                            <label class="block text-[12px]  text-gray-800 font-medium leading-[24px]" for="comment"> Comment </label>
                                            <p class="text-gray-800" v-text="review.comment"></p>
                                        </div>
                    
                                        <div class="w-full" v-if="review.images.length">
                                            <x-admin::form.control-group.label>
                                                Images
                                            </x-admin::form.control-group.label>
                
                                            <div class="flex gap-4">   
                                                <img
                                                    class="h-[60px] w-[60px] rounded-[4px]" 
                                                    v-for="image in review.images"
                                                    :src="image.url"
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </x-slot:content>
                            </x-admin::drawer>
                        </form>
                    </x-admin::form>
                </div>
            </div>
        </script>

        <script type="module">
            app.component('v-review-edit-drawer', {
                template: '#v-review-edit-drawer-template',

                data() {
                    return {
                        review: {},
                    }
                },

                methods: {
                    edit (id) {
                        this.$axios.get(`{{ route('admin.customer.review.edit', '') }}/${id}`)
                            .then((response) => {
                                this.$refs.review.open(),

                                this.review = response.data.data
                            })
                            .catch(error => {
                                if (error.response.status ==422) {
                                    setErrors(error.response.data.errors);
                                }
                            });
                   
                    },

                    update(params) {
                        this.$axios.post(`{{ route('admin.customer.review.update', '') }}/${params.id}`, params)
                            .then((response) => {
                                this.$refs.review.close();

                                this.$refs.review_data.get();
                            })
                            .catch(error => {
                                if (error.response.status == 422) {
                                    setErrors(error.response.data.errors);
                                }
                            });
                    }
                }
            })
        </script>
    @endPushOnce
</x-admin::layouts>