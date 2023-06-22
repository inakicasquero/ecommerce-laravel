<div class="flex justify-between items-center">
    <h2 class="w-[200px] h-[39px] shimmer"></h2>

    <div class="w-[150px] h-[50px] shimmer rounded-[12px]"></div>
</div>

<div class="grid mt-[60px] overflow-auto journal-scroll">
    <!-- Single row -->
    <div class="flex items-center max-w-full border-b-[1px] border-[#E9E9E9] ">
        <div class="min-w-[304px] max-w-full">
            <p class="w-[55%] h-[21px] bg-[#E9E9E9] shimmer"></p>
        </div>

        <div class="flex gap-[12px] border-l-[1px] border-[#E9E9E9]">
            <x-shop::shimmer.products.cards.grid
                class="min-w-[311px] max-w-[311px] pt-0 pr-0 p-[20px]"
                count="3"
            ></x-shop::shimmer.products.cards.grid>
        </div>
    </div>

    <!-- Single row -->
    <x-shop::shimmer.compare.attribute></x-shop::shimmer.compare.attribute>
</div>