<x-admin::layouts>
    <div class="flex-1 h-full max-w-full px-[16px] pt-[11px] pb-[22px] pl-[275px] max-lg:px-[16px]">
        
        <x-shop::form 
            :action="route('admin.locales.update', $locale->id)"
            enctype="multipart/form-data"
        >
            @method('PUT')

            <div class="flex  gap-[16px] justify-between items-center max-sm:flex-wrap">
				<p class="text-[20px] text-gray-800 font-bold">
                    @lang('Edit Locale')
                </p>

				<div class="flex gap-x-[10px] items-center">
                    <button 
                        type="submit"
                        class="px-[12px] py-[6px] bg-blue-600 border border-blue-700 rounded-[6px] text-gray-50 font-semibold cursor-pointer"
                    >
                        @lang('Save Locale')
                    </button>
				</div>
			</div>

            <div class="flex gap-[10px] mt-[14px] max-xl:flex-wrap">
				<div class="flex flex-col gap-[8px] flex-1 max-xl:flex-auto">
					<div class="p-[16px] bg-white rounded-[4px] box-shadow">

                        {!! view_render_event('bagisto.admin.settings.locale.edit.before', ['locale' => $locale]) !!}
                        
                        <p class="text-[16px] text-gray-800 font-semibold mb-[16px]">
                            @lang('General')
                        </p>

                        <x-admin::form.control-group.control
                            type="hidden"
                            name="code"
                            :value="old('code') ?? $locale->code"
                        >
                        </x-admin::form.control-group.control>

                        <x-admin::form.control-group class="mb-[10px]">
                            <x-admin::form.control-group.label>
                                Code
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="text"
                                name="code"
                                :value="old('code') ?? $locale->code"
                                id="code"
                                rules="required"
                                label="Code"
                                :placeholder="trans('Code')"
                                disabled="disabled"
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
                                :value="old('name') ?? $locale->name"
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
                                Direction
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="select"
                                name="direction"
                                :value="old('direction') ?? $locale->direction"
                                id="direction"
                                rules="required"
                                label="direction"
                            >
                                <option 
                                    value="ltr"
                                    title="Text direction left to right"
                                    {{ (old('direction') ?? $locale->direction) == 'ltr' ? 'selected' : '' }}
                                >
                                    LTR
                                </option>
            
                                <option 
                                    value="rtl"
                                    title="Text direction right to left"
                                    {{ (old('direction') ?: $locale->direction) == 'rtl' ? 'selected' : '' }}
                                >
                                    RTL
                                </option>
                            </x-admin::form.control-group.control>

                            <x-admin::form.control-group.error
                                control-name="direction"
                            >
                            </x-admin::form.control-group.error>
                        </x-admin::form.control-group>
            
                        <x-admin::form.control-group class="mb-[10px]">
                            <x-admin::form.control-group.label>
                                @lang('Locale Logo')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="image"
                                name="logo_path[image_1]"
                                id="direction"
                                :label="trans('Logo Path')"
                                :src="$locale->logo_url"
                                accepted-types="image/*"
                            >
                            </x-admin::form.control-group.control>

                            <x-admin::form.control-group.error
                                control-name="logo_path[image_1]"
                            >
                            </x-admin::form.control-group.error>
                        </x-admin::form.control-group>
    
                        {!! view_render_event('bagisto.admin.settings.locale.edit.after', ['locale' => $locale]) !!}
                    </div>
				</div>
			</div>
        </x-admin::form>
    </div>
</x-admin::layouts>