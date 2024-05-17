<v-datagrid-filter
    :src="src"
    :is-loading="isLoading"
    :available="available"
    :applied="applied"
    @applyFilter="filter"
    @removeFilter="filter"
    @applySavedFilter="applySavedFilter"
>
    {{ $slot }}
</v-datagrid-filter>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-datagrid-filter-template"
    >
        <slot
            name="filter"
            :available="available"
            :applied="applied"
            :filters="filters"
            :apply-filter="applyFilter"
            :apply-column-values="applyColumnValues"
            :find-applied-column="findAppliedColumn"
            :has-any-applied-column-values="hasAnyAppliedColumnValues"
            :get-applied-column-values="getAppliedColumnValues"
            :remove-applied-column-value="removeAppliedColumnValue"
            :remove-applied-column-all-values="removeAppliedColumnAllValues"
        >
            <template v-if="isLoading">
                <x-admin::shimmer.datagrid.toolbar.filter />
            </template>

            <template v-else>
                <x-admin::drawer
                    width="350px"
                    ref="filterDrawer"
                >
                    <x-slot:toggle>
                        <div>
                            <div
                                class="relative inline-flex w-full max-w-max cursor-pointer select-none appearance-none items-center justify-between gap-x-1 rounded-md border bg-white px-1 py-1.5 text-center text-gray-600 transition-all marker:shadow hover:border-gray-400 focus:outline-none focus:ring-2 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 ltr:pl-3 ltr:pr-5 rtl:pl-5 rtl:pr-3"
                                :class="{'[&>*]:text-blue-600 [&>*]:dark:text-white': filters.columns.length > 0}"
                            >
                                <span class="icon-filter text-2xl"></span>

                                <span>
                                    @lang('admin::app.components.datagrid.toolbar.filter.title')
                                </span>

                                <span
                                    class="icon-dot absolute right-2 top-1.5 text-sm font-bold"
                                    v-if="filters.columns.length > 0"
                                >
                                </span>
                            </div>

                            <div class="z-10 hidden w-full divide-y divide-gray-100 rounded bg-white shadow dark:bg-gray-900">
                            </div>
                        </div>
                    </x-slot>

                    <x-slot:header>
                        <div class="flex items-center justify-between p-2">
                            <p class="text-xl font-semibold text-gray-800 dark:text-white">
                                @lang('admin::app.components.datagrid.filters.title')
                            </p>
                        </div>
                    </x-slot>

                    <x-slot:content class="!p-0">
                        <template v-if="! isShowSavedFilters">
                            <x-admin::accordion 
                                class="!rounded-none !shadow-none" 
                                v-if="savedFilters.available.length > 0"
                            >
                                <x-slot:header class="px-4 text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('admin::app.components.datagrid.toolbar.filter.quick-filters')
                                </x-slot>

                                <x-slot:content class="!p-0">
                                    <div class="!p-0">
                                        <div v-for="(filter,index) in savedFilters.available">
                                            <div
                                                class="flex items-center justify-between px-4 py-1.5 text-sm text-gray-700 hover:bg-gray-50 dark:text-white dark:hover:bg-gray-950"
                                                :class="{ 'bg-gray-50 dark:bg-gray-950 font-semibold': applied.savedFilterId == filter.id }"
                                            >
                                                <span 
                                                    class="cursor-pointer" 
                                                    @click="applySavedFilter(filter)"
                                                >
                                                    @{{ filter.name }}
                                                </span>

                                                <span
                                                    class="icon-cross cursor-pointer rounded p-1.5 text-xl hover:bg-gray-200 dark:hover:bg-gray-800"
                                                    @click="deleteSavedFilter(filter)"
                                                >
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </x-slot>
                            </x-admin::accordion>

                            <x-admin::accordion class="!rounded-none !shadow-none">
                                <x-slot:header class="px-4 text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('admin::app.components.datagrid.toolbar.filter.custom-filters')
                                </x-slot>

                                <x-slot:content class="!p-0">
                                    <div class="!p-5">
                                        <div v-for="column in available.columns">
                                            <div v-if="column.filterable">
                                                <!-- Boolean -->
                                                <div v-if="column.type === 'boolean'">
                                                    <div class="flex items-center justify-between">
                                                        <p
                                                            class="text-xs font-medium text-gray-800 dark:text-white"
                                                            v-text="column.label"
                                                        >
                                                        </p>

                                                        <div
                                                            class="flex items-center gap-x-1.5"
                                                            @click="removeAppliedColumnAllValues(column.index)"
                                                        >
                                                            <p
                                                                class="cursor-pointer text-xs font-medium leading-6 text-blue-600"
                                                                v-if="hasAnyAppliedColumnValues(column.index)"
                                                            >
                                                                @lang('admin::app.components.datagrid.filters.custom-filters.clear-all')
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="mb-2 mt-1.5">
                                                        <x-admin::dropdown>
                                                            <x-slot:toggle>
                                                                <button
                                                                    type="button"
                                                                    class="inline-flex w-full cursor-pointer appearance-none items-center justify-between gap-x-2 rounded-md border bg-white px-2.5 py-1.5 text-center leading-6 text-gray-600 transition-all marker:shadow hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                                                >
                                                                    <span
                                                                        class="text-sm text-gray-400 dark:text-gray-400"
                                                                        v-text="'@lang('admin::app.components.datagrid.filters.select')'"
                                                                    >
                                                                    </span>

                                                                    <span class="icon-sort-down text-2xl"></span>
                                                                </button>
                                                            </x-slot>

                                                            <x-slot:menu>
                                                                <x-admin::dropdown.menu.item
                                                                    v-for="option in column.options"
                                                                    v-text="option.label"
                                                                    @click="applyFilter(option.value, column)"
                                                                >
                                                                </x-admin::dropdown.menu.item>
                                                            </x-slot>
                                                        </x-admin::dropdown>
                                                    </div>

                                                    <div class="mb-4 flex flex-wrap gap-2">
                                                        <p
                                                            class="flex items-center rounded bg-gray-600 px-2 py-1 font-semibold text-white"
                                                            v-for="appliedColumnValue in getAppliedColumnValues(column.index)"
                                                        >
                                                            <!-- Retrieving the label from the options based on the applied column value. -->
                                                            <span v-text="column.options.find((option => option.value == appliedColumnValue)).label"></span>

                                                            <span
                                                                class="icon-cross cursor-pointer text-lg text-white ltr:ml-1.5 rtl:mr-1.5"
                                                                @click="removeAppliedColumnValue(column.index, appliedColumnValue)"
                                                            >
                                                            </span>
                                                        </p>
                                                    </div>
                                                </div>

                                                <!-- Dropdown -->
                                                <div v-else-if="column.type === 'dropdown'">
                                                    <!-- Basic -->
                                                    <div v-if="column.options.type === 'basic'">
                                                        <div class="flex items-center justify-between">
                                                            <p
                                                                class="text-xs font-medium text-gray-800 dark:text-white"
                                                                v-text="column.label"
                                                            >
                                                            </p>

                                                            <div
                                                                class="flex items-center gap-x-1.5"
                                                                @click="removeAppliedColumnAllValues(column.index)"
                                                            >
                                                                <p
                                                                    class="cursor-pointer text-xs font-medium leading-6 text-blue-600"
                                                                    v-if="hasAnyAppliedColumnValues(column.index)"
                                                                >
                                                                    @lang('admin::app.components.datagrid.filters.custom-filters.clear-all')
                                                                </p>
                                                            </div>
                                                        </div>

                                                        <div class="mb-2 mt-1.5">
                                                            <x-admin::dropdown>
                                                                <x-slot:toggle>
                                                                    <button
                                                                        type="button"
                                                                        class="inline-flex w-full cursor-pointer appearance-none items-center justify-between gap-x-2 rounded-md border bg-white px-2.5 py-1.5 text-center leading-6 text-gray-600 transition-all marker:shadow hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                                                    >
                                                                        <span
                                                                            class="text-sm text-gray-400 dark:text-gray-400"
                                                                            v-text="'@lang('admin::app.components.datagrid.filters.select')'"
                                                                        >
                                                                        </span>

                                                                        <span class="icon-sort-down text-2xl"></span>
                                                                    </button>
                                                                </x-slot>

                                                                <x-slot:menu>
                                                                    <x-admin::dropdown.menu.item
                                                                        v-for="option in column.options.params.options"
                                                                        v-text="option.label"
                                                                        @click="applyFilter(option.value, column)"
                                                                    >
                                                                    </x-admin::dropdown.menu.item>
                                                                </x-slot>
                                                            </x-admin::dropdown>
                                                        </div>

                                                        <div class="mb-4 flex flex-wrap gap-2">
                                                            <p
                                                                class="flex items-center rounded bg-gray-600 px-2 py-1 font-semibold text-white"
                                                                v-for="appliedColumnValue in getAppliedColumnValues(column.index)"
                                                            >
                                                                <!-- Retrieving the label from the options based on the applied column value. -->
                                                                <span v-text="column.options.params.options.find((option => option.value == appliedColumnValue)).label"></span>

                                                                <span
                                                                    class="icon-cross cursor-pointer text-lg text-white ltr:ml-1.5 rtl:mr-1.5"
                                                                    @click="removeAppliedColumnValue(column.index, appliedColumnValue)"
                                                                >
                                                                </span>
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <!-- Searchable -->
                                                    <div v-else-if="column.options.type === 'searchable'">
                                                        <div class="flex items-center justify-between">
                                                            <p
                                                                class="text-xs font-medium text-gray-800 dark:text-white"
                                                                v-text="column.label"
                                                            >
                                                            </p>

                                                            <div
                                                                class="flex items-center gap-x-1.5"
                                                                @click="removeAppliedColumnAllValues(column.index)"
                                                            >
                                                                <p
                                                                    class="cursor-pointer text-xs font-medium leading-6 text-blue-600"
                                                                    v-if="hasAnyAppliedColumnValues(column.index)"
                                                                >
                                                                    @lang('admin::app.components.datagrid.filters.custom-filters.clear-all')
                                                                </p>
                                                            </div>
                                                        </div>

                                                        <div class="mb-2 mt-1.5">
                                                            <v-datagrid-searchable-dropdown
                                                                :datagrid-id="available.id"
                                                                :column="column"
                                                                @select-option="applyFilter($event, column)"
                                                            >
                                                            </v-datagrid-searchable-dropdown>
                                                        </div>

                                                        <div class="mb-4 flex flex-wrap gap-2">
                                                            <p
                                                                class="flex items-center rounded bg-gray-600 px-2 py-1 font-semibold text-white"
                                                                v-for="appliedColumnValue in getAppliedColumnValues(column.index)"
                                                            >
                                                                <span v-text="appliedColumnValue"></span>

                                                                <span
                                                                    class="icon-cross cursor-pointer text-lg text-white ltr:ml-1.5 rtl:mr-1.5"
                                                                    @click="removeAppliedColumnValue(column.index, appliedColumnValue)"
                                                                >
                                                                </span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Date Range -->
                                                <div v-else-if="column.type === 'date_range'">
                                                    <div class="flex items-center justify-between">
                                                        <p
                                                            class="text-xs font-medium text-gray-800 dark:text-white"
                                                            v-text="column.label"
                                                        >
                                                        </p>

                                                        <div
                                                            class="flex items-center gap-x-1.5"
                                                            @click="removeAppliedColumnAllValues(column.index)"
                                                        >
                                                            <p
                                                                class="cursor-pointer text-xs font-medium leading-6 text-blue-600"
                                                                v-if="hasAnyAppliedColumnValues(column.index)"
                                                            >
                                                                @lang('admin::app.components.datagrid.filters.custom-filters.clear-all')
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="mt-1.5 grid grid-cols-2 gap-1.5">
                                                        <p
                                                            class="cursor-pointer rounded-md border px-3 py-2 text-center text-sm font-medium leading-6 text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:text-gray-300 dark:hover:border-gray-400"
                                                            v-for="option in column.options"
                                                            v-text="option.label"
                                                            @click="applyFilter(
                                                                $event,
                                                                column,
                                                                { quickFilter: { isActive: true, selectedFilter: option } }
                                                            )"
                                                        >
                                                        </p>

                                                        <x-admin::flat-picker.date ::allow-input="false">
                                                            <input
                                                                value=""
                                                                class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                                                                :type="column.input_type"
                                                                :name="`${column.index}[from]`"
                                                                :placeholder="column.label"
                                                                :ref="`${column.index}[from]`"
                                                                @change="applyFilter(
                                                                    $event,
                                                                    column,
                                                                    { range: { name: 'from' }, quickFilter: { isActive: false } }
                                                                )"
                                                            />
                                                        </x-admin::flat-picker.date>

                                                        <x-admin::flat-picker.date ::allow-input="false">
                                                            <input
                                                                type="column.input_type"
                                                                value=""
                                                                class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                                                                :name="`${column.index}[to]`"
                                                                :placeholder="column.label"
                                                                :ref="`${column.index}[from]`"
                                                                @change="applyFilter(
                                                                    $event,
                                                                    column,
                                                                    { range: { name: 'to' }, quickFilter: { isActive: false } }
                                                                )"
                                                            />
                                                        </x-admin::flat-picker.date>

                                                        <div class="mb-4 flex flex-wrap gap-2">
                                                            <p
                                                                class="flex items-center rounded bg-gray-600 px-2 py-1 font-semibold text-white"
                                                                v-for="appliedColumnValue in getAppliedColumnValues(column.index)"
                                                            >
                                                                <span v-text="appliedColumnValue.join(' to ')"></span>

                                                                <span
                                                                    class="icon-cross cursor-pointer text-lg text-white ltr:ml-1.5 rtl:mr-1.5"
                                                                    @click="removeAppliedColumnValue(column.index, appliedColumnValue)"
                                                                >
                                                                </span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Date Time Range -->
                                                <div v-else-if="column.type === 'datetime_range'">
                                                    <div class="flex items-center justify-between">
                                                        <p
                                                            class="text-xs font-medium text-gray-800 dark:text-white"
                                                            v-text="column.label"
                                                        >
                                                        </p>

                                                        <div
                                                            class="flex items-center gap-x-1.5"
                                                            @click="removeAppliedColumnAllValues(column.index)"
                                                        >
                                                            <p
                                                                class="cursor-pointer text-xs font-medium leading-6 text-blue-600"
                                                                v-if="hasAnyAppliedColumnValues(column.index)"
                                                            >
                                                                @lang('admin::app.components.datagrid.filters.custom-filters.clear-all')
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="my-4 grid grid-cols-2 gap-1.5">
                                                        <p
                                                            class="cursor-pointer rounded-md border px-3 py-2 text-center text-sm font-medium leading-6 text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:text-gray-300 dark:hover:border-gray-400"
                                                            v-for="option in column.options"
                                                            v-text="option.label"
                                                            @click="applyFilter(
                                                                $event,
                                                                column,
                                                                { quickFilter: { isActive: true, selectedFilter: option } }
                                                            )"
                                                        >
                                                        </p>

                                                        <x-admin::flat-picker.datetime ::allow-input="false">
                                                            <input
                                                                value=""
                                                                class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                                                                :type="column.input_type"
                                                                :name="`${column.index}[from]`"
                                                                :placeholder="column.label"
                                                                :ref="`${column.index}[from]`"
                                                                @change="applyFilter(
                                                                    $event,
                                                                    column,
                                                                    { range: { name: 'from' }, quickFilter: { isActive: false } }
                                                                )"
                                                            />
                                                        </x-admin::flat-picker.datetime>

                                                        <x-admin::flat-picker.datetime ::allow-input="false">
                                                            <input
                                                                type="column.input_type"
                                                                value=""
                                                                class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                                                                :name="`${column.index}[to]`"
                                                                :placeholder="column.label"
                                                                :ref="`${column.index}[from]`"
                                                                @change="applyFilter(
                                                                    $event,
                                                                    column,
                                                                    { range: { name: 'to' }, quickFilter: { isActive: false } }
                                                                )"
                                                            />
                                                        </x-admin::flat-picker.datetime>

                                                        <div class="mb-4 flex flex-wrap gap-2">
                                                            <p
                                                                class="flex items-center rounded bg-gray-600 px-2 py-1 font-semibold text-white"
                                                                v-for="appliedColumnValue in getAppliedColumnValues(column.index)"
                                                            >
                                                                <span v-text="appliedColumnValue.join(' to ')"></span>

                                                                <span
                                                                    class="icon-cross cursor-pointer text-lg text-white ltr:ml-1.5 rtl:mr-1.5"
                                                                    @click="removeAppliedColumnValue(column.index, appliedColumnValue)"
                                                                >
                                                                </span>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Rest -->
                                                <div v-else>
                                                    <div class="flex items-center justify-between">
                                                        <p
                                                            class="text-xs font-medium text-gray-800 dark:text-white"
                                                            v-text="column.label"
                                                        >
                                                        </p>

                                                        <div
                                                            class="flex items-center gap-x-1.5"
                                                            @click="removeAppliedColumnAllValues(column.index)"
                                                        >
                                                            <p
                                                                class="cursor-pointer text-xs font-medium leading-6 text-blue-600"
                                                                v-if="hasAnyAppliedColumnValues(column.index)"
                                                            >
                                                                @lang('admin::app.components.datagrid.filters.custom-filters.clear-all')
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <div class="mb-2 mt-1.5 grid">
                                                        <input
                                                            type="text"
                                                            class="block w-full rounded-md border bg-white px-2 py-1.5 text-sm leading-6 text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                                            :name="column.index"
                                                            :placeholder="column.label"
                                                            @keyup.enter="applyFilter($event, column)"
                                                        />
                                                    </div>

                                                    <div class="mb-4 flex flex-wrap gap-2">
                                                        <p
                                                            class="flex items-center rounded bg-gray-600 px-2 py-1 font-semibold text-white"
                                                            v-for="appliedColumnValue in getAppliedColumnValues(column.index)"
                                                        >
                                                            <span v-text="appliedColumnValue"></span>

                                                            <span
                                                                class="icon-cross cursor-pointer text-lg text-white ltr:ml-1.5 rtl:mr-1.5"
                                                                @click="removeAppliedColumnValue(column.index, appliedColumnValue)"
                                                            >
                                                            </span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <button
                                            type="button"
                                            v-if="filters.columns.length > 0"
                                            class="secondary-button w-full"
                                            @click="isShowSavedFilters = ! isShowSavedFilters"
                                        >
                                            @lang('admin::app.components.datagrid.toolbar.filter.save-filter')
                                        </button>
                                    </div>
                                </x-slot>
                            </x-admin::accordion>
                        </template>

                        <template v-else>
                            <div class="flex items-center justify-between p-3 px-5">
                                <p class="text-base font-semibold text-gray-800 dark:text-white">
                                    @lang('admin::app.components.datagrid.toolbar.filter.create-new-filter')
                                </p>
                            </div>

                            <div 
                                class="px-5 py-1" 
                                v-if="filters.columns.length > 0"
                            >
                                <x-admin::form
                                    v-slot="{ meta, errors, handleSubmit }"
                                    as="div"
                                >
                                    <form @submit="handleSubmit($event, saveFilters)">
                                        <x-admin::form.control-group>
                                            <x-admin::form.control-group.label class="required">
                                                @lang('admin::app.components.datagrid.toolbar.filter.name')
                                            </x-admin::form.control-group.label>

                                            <x-admin::form.control-group.control
                                                type="text"
                                                name="name"
                                                id="name"
                                                rules="required"
                                                :label="trans('admin::app.components.datagrid.toolbar.filter.name')"
                                                :placeholder="trans('admin::app.components.datagrid.toolbar.filter.name')"
                                            />

                                            <x-admin::form.control-group.error control-name="name" />
                                        </x-admin::form.control-group>

                                        <div class="flex content-end items-center justify-end gap-4">
                                            <div
                                                @click="isShowSavedFilters = ! isShowSavedFilters"
                                                class="secondary-button"
                                            >
                                                @lang('admin::app.components.datagrid.toolbar.filter.back-btn')
                                            </div>
                                            
                                            <button
                                                type="submit"
                                                class="primary-button"
                                                aria-label="@lang('admin::app.components.datagrid.toolbar.filter.save-btn')"
                                                :disabled="savedFilters.params.filters.columns.every(column => column.value.length === 0)"
                                            >
                                                @lang('admin::app.components.datagrid.toolbar.filter.save-btn')
                                            </button>
                                        </div>
                                        
                                        <div v-for="column in savedFilters.params.filters.columns">
                                            <p class="py-4 text-base font-semibold text-gray-800 dark:text-white">
                                                @lang('Select Filters')
                                            </p>
                                            
                                            <div v-if="column.value.length > 0" >
                                                <p class="mb-2 text-xs font-medium text-gray-800 dark:text-white">
                                                    @{{ column.label }}
                                                </p>
                                                
                                                <div class="mb-4 flex flex-wrap gap-2">
                                                    <p
                                                        v-for="columnValue in column.value"
                                                        class="flex items-center rounded bg-gray-600 px-2 py-1 font-semibold text-white"
                                                    >
                                                        <span v-text="columnValue"></span>

                                                        <div>
                                                            <span
                                                                class="icon-cross cursor-pointer text-lg text-white ltr:ml-1.5 rtl:mr-1.5"
                                                                @click="removeSavedFilterColumnValue(column, columnValue)"
                                                            >
                                                            </span>
                                                        </div>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </x-admin::form>
                            </div>
                        </template>
                    </x-slot>
                </x-admin::drawer>
            </template>
        </slot>
    </script>

    <script type="module">
        app.component('v-datagrid-filter', {
            template: '#v-datagrid-filter-template',

            props: ['isLoading', 'available', 'applied', 'src', 'savedFilter'],

            data() {
                return {
                    savedFilters: {
                        available: [],

                        applied: null,

                        params: {
                            filters: {
                                columns: [],
                            },
                        },
                    },

                    filters: {
                        columns: [],
                    },

                    isShowSavedFilters: false,
                };
            },

            mounted() {
                this.filters.columns = this.applied.filters.columns.filter((column) => column.index !== 'all');

                this.savedFilters.params.filters.columns = JSON.parse(JSON.stringify(this.filters.columns));

                this.getSavedFilters();
            },

            methods: {
                /**
                 * Save filters to the database.
                 *
                 * @returns {void}
                 */
                 saveFilters(params, { setErrors }) {
                    let applied = JSON.parse(JSON.stringify(this.applied));

                    applied.filters.columns = this.savedFilters.params.filters.columns.filter((column) => column.value.length > 0);;

                    this.$axios.post('{{ route('datagrid.filters.store') }}', {
                        src: this.src,
                        name: params.name,
                        applied,
                    })
                        .then(response => {
                            this.savedFilters.available.push(response.data.data);

                            this.savedFilters.name = '';

                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                            this.isShowSavedFilters = false;
                        })
                        .catch(error => {
                            if (error.response.status == 422) {
                                setErrors(error.response.data.errors);
                            } else {
                                this.$emitter.emit('add-flash', { type: 'error',  message: response.data.message });
                            }
                        });
                },

                /**
                 * Remove filter option from save filters.
                 *
                 * @returns {void}
                 */
                removeSavedFilterColumnValue(column, value) {
                    column.value = column.value.filter((columnValue) => columnValue !== value);
                },

                /**
                 * Retrieves the saved filters.
                 *
                 * @returns {void}
                 */
                getSavedFilters() {
                    this.$axios
                        .get('{{ route('datagrid.filters.index') }}', {
                            params: { src: this.src }
                        })
                        .then(response => {
                            this.savedFilters.available = response.data;
                        })
                        .catch(error => {});
                },

                /**
                 * Applies the saved filter.
                 *
                 * @param {Object} filter - The filter to be applied.
                 */
                applySavedFilter(filter) {
                    this.$emit('applySavedFilter', filter);
                },

                /**
                 * Deletes the saved filter.
                 */
                deleteSavedFilter(filter) {
                    this.$emitter.emit('open-confirm-modal', {
                        agree: () => {
                            this.$axios.delete(`{{ route('datagrid.filters.destroy', '') }}/${filter.id}`)
                            
                            .then(response => {
                                this.savedFilters.available = this.savedFilters.available.filter((savedFilter) => savedFilter.id !== filter.id);

                                this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                            })
                            .catch(error => {
                                this.$emitter.emit('add-flash', { type: 'error', message: response.data.message });
                            });
                        }
                    });
                },

                /**
                 * Apply filter.
                 *
                 * @param {Event} $event
                 * @param {object} column
                 * @param {object} additional
                 * @returns {void}
                 */
                applyFilter($event, column = null, additional = {}) {
                    let quickFilter = additional?.quickFilter;

                    if (quickFilter?.isActive) {
                        let options = quickFilter.selectedFilter;

                        switch (column.type) {
                            case 'date_range':
                            case 'datetime_range':
                                this.applyColumnValues(column, options.from, {
                                    range: {
                                        name: 'from'
                                    }
                                });

                                this.applyColumnValues(column, options.to, {
                                    range: {
                                        name: 'to'
                                    }
                                });

                                break;

                            default:
                                break;
                        }
                    } else {
                        /**
                         * Here, either a real event will come or a string value. If a string value is present, then
                         * we create a similar event-like structure to avoid any breakage and make it easy to use.
                         */
                        if ($event?.target?.value === undefined) {
                            $event = {
                                target: {
                                    value: $event,
                                }
                            };
                        }

                        this.applyColumnValues(column, $event.target.value, additional);

                        if (column) {
                            $event.target.value = '';
                        }
                    }

                    this.$emit('applyFilter', this.filters);

                    this.$refs.filterDrawer.close();
                },

                /**
                 * Apply column values.
                 *
                 * @param {object} column
                 * @param {string} requestedValue
                 * @param {object} additional
                 * @returns {void}
                 */
                applyColumnValues(column, requestedValue, additional = {}) {
                    let appliedColumn = this.findAppliedColumn(column?.index);

                    if (
                        requestedValue === undefined ||
                        requestedValue === '' ||
                        appliedColumn?.value.includes(requestedValue)
                    ) {
                        return;
                    }

                    switch (column.type) {
                        case 'date_range':
                        case 'datetime_range':
                            let { range } = additional;

                            if (appliedColumn) {
                                let appliedRanges = appliedColumn.value[0];

                                if (range.name == 'from') {
                                    appliedRanges[0] = requestedValue;
                                }

                                if (range.name == 'to') {
                                    appliedRanges[1] = requestedValue;
                                }

                                appliedColumn.value = [appliedRanges];
                            } else {
                                let appliedRanges = ['', ''];

                                if (range.name == 'from') {
                                    appliedRanges[0] = requestedValue;
                                }

                                if (range.name == 'to') {
                                    appliedRanges[1] = requestedValue;
                                }

                                this.filters.columns.push({
                                    ...column,
                                    value: [appliedRanges]
                                });
                            }

                            break;

                        default:
                            if (appliedColumn) {
                                appliedColumn.value.push(requestedValue);
                            } else {
                                this.filters.columns.push({
                                    ...column,
                                    value: [requestedValue]
                                });
                            }

                            break;
                    }
                },

                /**
                 * Find applied column.
                 *
                 * @param {string} columnIndex
                 * @returns {object}
                 */
                findAppliedColumn(columnIndex) {
                    return this.filters.columns.find(column => column.index === columnIndex);
                },

                /**
                 * Check if any values are applied for the specified column.
                 *
                 * @param {string} columnIndex
                 * @returns {boolean}
                 */
                hasAnyAppliedColumnValues(columnIndex) {
                    let appliedColumn = this.findAppliedColumn(columnIndex);

                    return appliedColumn?.value.length > 0;
                },

                /**
                 * Get applied values for the specified column.
                 *
                 * @param {string} columnIndex
                 * @returns {Array}
                 */
                getAppliedColumnValues(columnIndex) {
                    let appliedColumn = this.findAppliedColumn(columnIndex);

                    return appliedColumn?.value ?? [];
                },

                /**
                 * Remove a specific value from the applied values of the specified column.
                 *
                 * @param {string} columnIndex
                 * @param {any} appliedColumnValue
                 * @returns {void}
                 */
                removeAppliedColumnValue(columnIndex, appliedColumnValue) {
                    let appliedColumn = this.findAppliedColumn(columnIndex);

                    appliedColumn.value = appliedColumn?.value.filter(value => value !== appliedColumnValue);

                    /**
                     * Clean up is done here. If there are no applied values present, there is no point in including the applied column as well.
                     */
                    if (! appliedColumn.value.length) {
                        this.filters.columns = this.filters.columns.filter(column => column.index !== columnIndex);
                    }

                    this.$emit('removeFilter', this.filters);

                    this.$refs.filterDrawer.close();
                },

                /**
                 * Remove all values from the applied values of the specified column.
                 *
                 * @param {string} columnIndex
                 * @returns {void}
                 */
                removeAppliedColumnAllValues(columnIndex) {
                    this.filters.columns = this.filters.columns.filter(column => column.index !== columnIndex);

                    this.$emit('removeFilter', this.filters);
                },
            },
        });
    </script>

    <script type="text/x-template" id="v-datagrid-searchable-dropdown-template">
        <x-admin::dropdown ::close-on-click="false">
            <x-slot:toggle>
                <button
                    type="button"
                    class="inline-flex w-full cursor-pointer appearance-none items-center justify-between gap-x-2 rounded-md border bg-white px-2.5 py-1.5 text-center leading-6 text-gray-600 transition-all marker:shadow hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                >
                    <span
                        class="text-sm text-gray-400 dark:text-gray-400"
                        v-text="'@lang('admin::app.components.datagrid.filters.select')'"
                    >
                    </span>

                    <span class="icon-sort-down text-2xl"></span>
                </button>
            </x-slot>

            <x-slot:menu>
                <div class="relative">
                    <div class="relative rounded">
                        <ul class="list-reset">
                            <li class="p-2">
                                <input
                                    class="block w-full rounded-md border bg-white px-2 py-1.5 text-sm leading-6 text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                                    @keyup="lookUp($event)"
                                >
                            </li>

                            <ul class="p-2">
                                <li v-if="! isMinimumCharacters">
                                    <p
                                        class="block p-2 text-gray-600 dark:text-gray-300"
                                        v-text="'@lang('admin::app.components.datagrid.filters.dropdown.searchable.atleast-two-chars')'"
                                    >
                                    </p>
                                </li>

                                <li v-else-if="! searchedOptions.length">
                                    <p
                                        class="block p-2 text-gray-600 dark:text-gray-300"
                                        v-text="'@lang('admin::app.components.datagrid.filters.dropdown.searchable.no-results')'"
                                    >
                                    </p>
                                </li>

                                <li
                                    v-for="option in searchedOptions"
                                    v-else
                                >
                                    <p
                                        class="cursor-pointer p-2 text-sm text-gray-600 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-950"
                                        v-text="option.label"
                                        @click="selectOption(option)"
                                    >
                                    </p>
                                </li>
                            </ul>
                        </ul>
                    </div>
                </div>
            </x-slot>
        </x-admin::dropdown>
    </script>

    <script type="module">
        app.component('v-datagrid-searchable-dropdown', {
            template: '#v-datagrid-searchable-dropdown-template',

            props: ['datagridId', 'column'],

            data() {
                return {
                    isMinimumCharacters: false,

                    searchedOptions: [],
                };
            },

            methods: {
                /**
                 * Perform a look up for options based on the search query.
                 *
                 * @param {Event} $event
                 * @returns {void}
                 */
                lookUp($event) {
                    let params = {
                        datagrid_id: this.datagridId,
                        column: this.column.index,
                        search: $event.target.value,
                    };

                    if (! (params['search'].length > 1)) {
                        this.searchedOptions = [];

                        this.isMinimumCharacters = false;

                        return;
                    }

                    this.$axios
                        .get('{{ route('admin.datagrid.look_up') }}', {
                            params
                        })
                        .then(({
                            data
                        }) => {
                            this.isMinimumCharacters = true;

                            this.searchedOptions = data;
                        });
                },

                /**
                 * Select an option from the searched options.
                 *
                 * @param {object} option
                 * @returns {void}
                 */
                selectOption(option) {
                    this.searchedOptions = [];

                    this.$emit('select-option', {
                        target: {
                            value: option.value
                        }
                    });
                },
            },
        });
    </script>
@endpushOnce
