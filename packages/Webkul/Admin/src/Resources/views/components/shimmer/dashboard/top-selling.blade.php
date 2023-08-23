<div class="flex flex-col gap-[32px] p-[16px]">
    @for ($i = 1; $i <= 3; $i++)
        <div class="flex gap-[10px] h-[65px]">
            {{-- Product Image --}}
            <div class="shimmer w-[65px] h-full rounded-[4px]"></div>

            <!-- Product Detailes -->
            <div class="flex flex-col gap-[6px] w-[251px]">
                {{-- Product Name --}}
                <div class="shimmer w-full h-[17px]"></div>

                <div class="flex justify-between">
                    {{-- Product Price --}}
                    <div class="shimmer w-[52px] h-[17px]"></div>

                    {{-- Grand Total --}}
                    <div class="shimmer w-[72px] h-[17px]"></div>
                </div>
            </div>
        </div>
    @endfor
</div>