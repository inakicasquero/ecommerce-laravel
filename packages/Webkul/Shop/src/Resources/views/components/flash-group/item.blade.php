<v-flash-item
    v-for='flash in flashes'
    :key='flash.uid'
    :flash="flash"
    @onRemove="remove($event)"
></v-flash-item>

@pushOnce('scripts')
    <script type="text/x-template" id="v-flash-item-template">
        <div
            class="flex gap-[46px] justify-between px-[20px] py-[12px] rounded-[8px] w-max max-w-[408px]"
            :style="typeStyles[flash.type]['container']"
        >
            <p
                class="text-[14px] flex break-all"
                :style="typeStyles[flash.type]['message']"
            >
                <span
                    class="icon-toast-done text-[24px] mr-[10px]"
                    :style="typeStyles[flash.type]['icon']"
                ></span>

                @{{ flash.message }}
            </p>

			<span
                class="icon-cancel cursor-pointer max-h-[16px] max-w-[16px]"
                :style="typeStyles[flash.type]['icon']"
                @click="remove"
            ></span>
        </div>
    </script>

    <script type="module">
        app.component('v-flash-item', {
            template: '#v-flash-item-template',

            props: ['flash'],

            data() {
                return {
                    typeStyles: {
                        success: {
                            container: 'background: #D4EDDA',

                            message: 'color: #155721',

                            icon: 'color: #155721'
                        },

                        error: {
                            container: 'background: #F8D7DA',

                            message: 'color: #721C24',

                            icon: 'color: #721C24'
                        },

                        warning: {
                            container: 'background: #FFF3CD',

                            message: 'color: #856404',

                            icon: 'color: #856404'
                        },

                        info: {
                            container: 'background: #E2E3E5',

                            message: 'color: #383D41',

                            icon: 'color: #383D41'
                        },
                    },
                };
            },

            created() {
                var self = this;

                setTimeout(function() {
                    self.remove()
                }, 5000)
            },

            methods: {
                remove() {
                    this.$emit('onRemove', this.flash)
                }
            }
        });
    </script>
@endpushOnce
