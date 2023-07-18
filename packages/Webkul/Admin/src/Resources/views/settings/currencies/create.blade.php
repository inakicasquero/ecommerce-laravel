<v-create></v-create>

@pushOnce('scripts')
    <script type="text/x-template" id="v-create-template">
        <div>
            <x-admin::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
            >
                <form @submit="handleSubmit($event, store)">
                    <x-admin::modal ref="currencyModal">
                        <x-slot:toggle>
                            <button 
                                type="button"
                                class="px-[12px] py-[6px] bg-blue-600 border border-blue-700 rounded-[6px] text-gray-50 font-semibold cursor-pointer"
                            >
                                @lang('admin::app.settings.currencies.add-title')
                            </button>
                        </x-slot:toggle>

                        <x-slot:header>
                            @lang('admin::app.settings.currencies.add-title')
                        </x-slot:header>

                        <x-slot:content>
                          <div class="flex gap-[10px] mt-[14px] max-xl:flex-wrap">
                            <div class="flex flex-col gap-[8px] flex-1 max-xl:flex-auto">
                                <div class="p-[16px] rounded-[4px]">

                                    {!! view_render_event('bagisto.admin.settings.currencies.create.before') !!}

                                    <p class="text-[16px] text-gray-800 font-semibold mb-[16px]">
                                        @lang('General')
                                    </p>

                                    <x-admin::form.control-group class="mb-[10px]">
                                        <x-admin::form.control-group.label>
                                            Code
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="text"
                                            name="code"
                                            :value="old('code')"
                                            id="code"
                                            rules="required"
                                            label="Code"
                                            :placeholder="trans('Code')"
                                        >
                                        </x-admin::form.control-group.control>

                                        <x-admin::form.control-group.error
                                            control-name="code"
                                        >
                                        </x-admin::form.control-group.error>
                                    </x-admin::form.control-group>

                                    <x-admin::form.control-group class="mb-[10px]">
                                        <x-admin::form.control-group.label>
                                            Name
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="text"
                                            name="name"
                                            :value="old('name')"
                                            id="name"
                                            rules="required"
                                            label="name"
                                            :placeholder="trans('Name')"
                                        >
                                        </x-admin::form.control-group.control>

                                        <x-admin::form.control-group.error
                                            control-name="name"
                                        >
                                        </x-admin::form.control-group.error>
                                    </x-admin::form.control-group>

                                    <x-admin::form.control-group class="mb-[10px]">
                                        <x-admin::form.control-group.label>
                                            Symbol
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="text"
                                            name="symbol"
                                            :value="old('symbol')"
                                            id="symbol"
                                            label="symbol"
                                            :placeholder="trans('Symbol')"
                                        >
                                        </x-admin::form.control-group.control>

                                        <x-admin::form.control-group.error
                                            control-name="symbol"
                                        >
                                        </x-admin::form.control-group.error>
                                    </x-admin::form.control-group>

                                    <x-admin::form.control-group class="mb-[10px]">
                                        <x-admin::form.control-group.label>
                                            Decimal
                                        </x-admin::form.control-group.label>

                                        <x-admin::form.control-group.control
                                            type="text"
                                            name="decimal"
                                            :value="old('decimal')"
                                            id="decimal"
                                            label="decimal"
                                            :placeholder="trans('Decimal')"
                                        >
                                        </x-admin::form.control-group.control>

                                        <x-admin::form.control-group.error
                                            control-name="decimal"
                                        >
                                        </x-admin::form.control-group.error>
                                    </x-admin::form.control-group>

                                    {!! view_render_event('bagisto.admin.settings.currencies.create.after') !!}
                                </div>
                            </div>
                        </div>
                        </x-slot:content>

                        <x-slot:footer>
                            <div class="flex gap-x-[10px] items-center">
                               <button 
                                    type="submit"
                                    class="px-[12px] py-[6px] bg-blue-600 border border-blue-700 rounded-[6px] text-gray-50 font-semibold cursor-pointer"
                                >
                                    @lang('Save Currency')
                                </button>
                            </div>
                        </x-slot:footer>
                    </x-admin::modal>
                </form>
            </x-admin::form>
        </div>
    </script>

    <script type="module">
        app.component('v-create', {
            template: '#v-create-template',
            methods: {
                store(params, { resetForm }) {
                    this.$axios.post('{{ route('admin.currencies.store') }}', params)
                        .then((response) => {
                            alert(response.data.data.message);

                            this.$refs.currencyModal.toggle();

                            resetForm();
                        }).catch((error) => console.log(error));
                },
            },
        });
    </script>
@endPushOnce