<v-media {{ $attributes }}>
    <x-admin::shimmer.image class="w-[110px] h-[110px] rounded-[4px]"></x-admin::shimmer.image>
</v-media>

@pushOnce('scripts')
    <script type="text/x-template" id="v-media-template">
        <!-- Panel Content -->
        <div class="grid">
            <div class="flex flex-wrap gap-[4px]">
                <!-- Upload Image Button -->
                <label
                    class="grid justify-items-center items-center w-full h-[120px] max-w-[120px] min-w-[120px] max-h-[120px] border border-dashed border-gray-300 rounded-[4px] cursor-pointer transition-all hover:border-gray-400"
                    :style="{'max-width': this.width, 'max-height': this.height}"
                    :for="$.uid + '_imageInput'"
                    v-if="allowMultiple || images.length == 0"
                >
                    <div class="flex flex-col items-center">
                        <span class="icon-image text-[24px]"></span>

                        <p class="grid text-[14px] text-gray-600 font-semibold text-center">
                            @lang('admin::app.catalog.products.edit.images.add-image-btn')
                            
                            <span class="text-[12px]">
                                @lang('admin::app.catalog.products.edit.images.allowed-types')
                            </span>
                        </p>

                        <input
                            type="file"
                            class="hidden"
                            :id="$.uid + '_imageInput'"
                            accept="image/*"
                            :multiple="allowMultiple"
                            :ref="$.uid + '_imageInput'"
                            @change="add"
                        />
                    </div>
                </label>

                <!-- Uploaded Images -->
                <div class="flex gap-[4px]">
                    <v-product-image-item
                        v-for="(image, index) in images"
                        :name="name"
                        :index="index"
                        :image="image"
                        :width="width"
                        :height="height"
                        @onRemove="remove($event)"
                    ></v-product-image-item>
                </div>
            </div>
        </div>  
    </script>

    <script type="text/x-template" id="v-product-image-item-template">
        <div class="grid justify-items-center min-w-[120px] max-h-[120px] relative rounded-[4px] overflow-hidden transition-all hover:border-gray-400 group">
            <!-- Image Preview -->
            <img
                :src="image.url"
                class="min-h-[120px]"
                :style="{'width': this.width, 'height': this.height}"
            />

            <div class="flex flex-col justify-between invisible w-full p-[11px] bg-white absolute top-0 bottom-0 opacity-80 transition-all group-hover:visible">
                <!-- Image Name -->
                <p class="text-[12px] text-gray-600 font-semibold break-all"></p>

                <!-- Actions -->
                <div class="flex justify-between">
                    <span
                        class="icon-delete text-[24px] p-[6px]  rounded-[6px] cursor-pointer hover:bg-gray-100"
                        @click="remove"
                    ></span>

                    <label
                        class="icon-edit text-[24px] p-[6px]  rounded-[6px] cursor-pointer hover:bg-gray-100"
                        :for="$.uid + '_imageInput_' + index"
                    ></label>

                    <input type="hidden" :name="name + '[' + image.id + ']'" v-if="! image.is_new"/>

                    <input
                        type="file"
                        :name="name + '[]'"
                        class="hidden"
                        accept="image/*"
                        :id="$.uid + '_imageInput_' + index"
                        :ref="$.uid + '_imageInput_' + index"
                        @change="edit"
                    />
                </div>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-media', {
            template: '#v-media-template',

            props: {
                name: {
                    type: String, 
                    default: 'attachments',
                }, 

                allowMultiple: {
                    type: Boolean,
                    default: false,
                },

                uploadedImages: {
                    type: Array,
                    default: () => []
                },

                width: {
                    type: String,
                    default: '120px'
                },

                height: {
                    type: String,
                    default: '120px'
                }
            },

            mounted() {
                this.images = this.uploadedImages;
            },

            data() {
                return {
                    images: [],
                }
            },

            methods: {
                add() {
                    let imageInput = this.$refs[this.$.uid + '_imageInput'];

                    if (imageInput.files == undefined) {
                        return;
                    }

                    const validFiles = Array.from(imageInput.files).every(file => file.type.includes('image/'));

                    if (! validFiles) {
                        this.$emitter.emit('add-flash', {
                            type: 'warning',
                            message: "{{ trans('admin::app.catalog.products.edit.images.not-allowed-error') }}"
                        });

                        return;
                    }

                    imageInput.files.forEach((file, index) => {
                        this.images.push({
                            id: 'image_' + this.images.length,
                            url: '',
                            file: file
                        });
                    });
                },

                remove(image) {
                    let index = this.images.indexOf(image);

                    this.images.splice(index, 1);
                }
            }
        });

        app.component('v-product-image-item', {
            template: '#v-product-image-item-template',

            props: ['index', 'image', 'name', 'width', 'height'],

            mounted() {
                if (this.image.file instanceof File) {
                    this.setFile(this.image.file);

                    this.readFile(this.image.file);
                }
            },

            methods: {
                edit() {
                    let imageInput = this.$refs[this.$.uid + '_imageInput_' + this.index];

                    if (imageInput.files == undefined) {
                        return;
                    }

                    const validFiles = Array.from(imageInput.files).every(file => file.type.includes('image/'));

                    if (! validFiles) {
                        this.$emitter.emit('add-flash', {
                            type: 'warning',
                            message: "{{ trans('admin::app.catalog.products.edit.images.not-allowed-error') }}"
                        });

                        return;
                    }

                    this.setFile(imageInput.files[0]);

                    this.readFile(imageInput.files[0]);
                },

                remove() {
                    this.$emit('onRemove', this.image)
                },

                setFile(file) {
                    this.image.is_new = 1;

                    const dataTransfer = new DataTransfer();

                    dataTransfer.items.add(file);

                    this.$refs[this.$.uid + '_imageInput_' + this.index].files = dataTransfer.files;
                },

                readFile(file) {
                    let reader = new FileReader();

                    reader.onload = (e) => {
                        this.image.url = e.target.result;
                    }

                    reader.readAsDataURL(file);
                }
            }
        });
    </script>
@endPushOnce