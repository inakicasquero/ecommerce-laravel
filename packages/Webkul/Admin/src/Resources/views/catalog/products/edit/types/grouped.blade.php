{!! view_render_event('bagisto.admin.catalog.product.edit.form.types.grouped.before', ['product' => $product]) !!}

<v-group-products :errors="errors"></v-group-products>

{!! view_render_event('bagisto.admin.catalog.product.edit.form.types.grouped.after', ['product' => $product]) !!}

@pushOnce('scripts')
    <script type="text/x-template" id="v-group-products-template">
        <div class="relative bg-white rounded-[4px] box-shadow">
            <!-- Panel Header -->
            <div class="flex gap-[20px] justify-between mb-[10px] p-[16px]">
                <div class="flex flex-col gap-[8px]">
                    <p class="text-[16px] text-gray-800 font-semibold">
                        @lang('admin::app.catalog.products.edit.types.grouped.title')
                    </p>

                    <p class="text-[12px] text-gray-500 font-medium">
                        @lang('admin::app.catalog.products.edit.types.grouped.info')
                    </p>
                </div>
                
                <!-- Add Button -->
                <div class="flex gap-x-[4px] items-center">
                    <div
                        class="px-[12px] py-[5px] bg-white border-[2px] border-blue-600 rounded-[6px] text-blue-600 font-semibold whitespace-nowrap cursor-pointer"
                        @click="$refs.productSearch.openDrawer()"
                    >
                        @lang('admin::app.catalog.products.edit.types.grouped.add-btn')
                    </div>
                </div>
            </div>

            <!-- Panel Content -->
            <div
                class="grid"
                v-if="groupProducts.length"
            >
                <!-- Draggable Products -->
                <draggable
                    ghost-class="draggable-ghost"
                    :list="groupProducts"
                    item-key="id"
                >
                    <template #item="{ element, index }">
                        <div class="flex gap-[10px] justify-between p-[16px] border-b-[1px] border-slate-300 cursor-pointer">
                            <!-- Information -->
                            <div class="flex gap-[10px]">
                                <!-- Drag Icon -->
                                <i class="icon-drag text-[20px] text-gray-600 transition-all pointer-events-none"></i>
                                
                                <!-- Image -->
                                <div class="grid gap-[4px] content-center justify-items-center min-w-[60px] h-[60px] px-[6px] border border-dashed border-gray-300 rounded-[4px]">
                                    <img src="{{ bagisto_asset('images/product-placeholders/top-angle.svg') }}" class="w-[20px]" />
                                    <p class="text-[6px] text-gray-400 font-semibold">Product Image</p>
                                </div>

                                <!-- Details -->
                                <div class="grid gap-[6px] place-content-start">
                                    <p class="text-[16x] text-gray-800 font-semibold">
                                        @{{ element.associated_product.name }}
                                    </p>

                                    <p class="text-gray-600">
                                        @{{ "@lang('admin::app.catalog.products.edit.types.grouped.sku')".replace(':sku', element.associated_product.sku) }}
                                    </p>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="grid gap-[4px] place-content-start text-right">
                                <p class="text-gray-800 font-semibold">
                                    @{{ $admin.formatPrice(element.associated_product.price) }}    
                                </p>

                                
                                <!-- Hidden Input -->
                                <input
                                    type="hidden"
                                    :name="'links[' + (element.id ? element.id : 'link_' + index) + '][associated_product_id]'"
                                    :value="element.associated_product.id"
                                />
                                
                                <input
                                    type="hidden"
                                    :name="'links[' + (element.id ? element.id : 'link_' + index) + '][sort_order]'"
                                    :value="index"
                                />

                                <x-admin::form.control-group class="!mb-0">
                                    <x-admin::form.control-group.label class="required">
                                        @lang('admin::app.catalog.products.edit.types.grouped.default-qty')
                                    </x-admin::form.control-group.label>

                                    <v-field
                                        type="text"
                                        :name="'links[' + (element.id ? element.id : 'link_' + index) + '][qty]'"
                                        v-model="element.qty"
                                        class="flex w-[86px] min-h-[39px] py-2 px-3 border rounded-[6px] text-[14px] text-gray-600 transition-all hover:border-gray-400"
                                        :class="[errors['links[' + (element.id ? element.id : 'link_' + index) + '][qty]'] ? 'border border-red-600 hover:border-red-600' : '']"
                                        rules="required|numeric|min_value:1"
                                    ></v-field>
                                </x-admin::form.control-group>

                                <p
                                    class="text-red-600 cursor-pointer"
                                    @click="remove(element)"
                                >
                                    @lang('admin::app.catalog.products.edit.types.grouped.delete')
                                </p>
                            </div>
                        </div>
                    </template>
                </draggable>
            </div>

            <!-- For Empty Variations -->
            <div
                class="grid gap-[14px] justify-center justify-items-center py-[40px] px-[10px]"
                v-else
            >
                <!-- Placeholder Image -->
                <img
                    src="{{ bagisto_asset('images/icon-add-product.svg') }}"
                    class="w-[80px] h-[80px] border border-dashed border-gray-300 rounded-[4px]"
                />

                <!-- Add Variants Information -->
                <div class="flex flex-col items-center">
                    <p class="text-[16px] text-gray-400 font-semibold">
                        @lang('admin::app.catalog.products.edit.types.grouped.empty-title')
                    </p>

                    <p class="text-gray-400">
                        @lang('admin::app.catalog.products.edit.types.grouped.empty-info')
                    </p>
                </div>
                
                <!-- Add Row Button -->
                <div
                    class="max-w-max px-[12px] py-[5px] bg-white border-[2px] border-blue-600 rounded-[6px] text-[14px] text-blue-600 font-semibold whitespace-nowrap cursor-pointer"
                    @click="$refs.productSearch.openDrawer()"
                >
                    @lang('admin::app.catalog.products.edit.types.grouped.add-btn')
                </div>
            </div>

            <!-- Product Search Blade Component -->
            <x-admin::products.search
                ref="productSearch"
                ::added-product-ids="addedProductIds"
                ::query-params="{type: 'simple'}"
                @onProductAdded="addSelected($event)"
            ></x-admin::products.search>
        </div>
    </script>

    <script type="module">
        app.component('v-group-products', {
            template: '#v-group-products-template',

            props: ['errors'],

            data() {
                return {
                    groupProducts: @json($product->grouped_products()->with('associated_product.inventory_indices')->orderBy('sort_order', 'asc')->get())
                }
            },

            computed: {
                addedProductIds() {
                    return this.groupProducts.map(product => product.associated_product.id);
                }
            },

            methods: {
                addSelected(selectedProducts) {
                    let self = this;

                    selectedProducts.forEach(function (product) {
                        self.groupProducts.push({
                            associated_product: product,
                            qty: 1,
                        });
                    });
                },

                remove(product) {
                    let index = this.groupProducts.indexOf(product)

                    this.groupProducts.splice(index, 1)
                },
            }
        });
    </script>
@endPushOnce