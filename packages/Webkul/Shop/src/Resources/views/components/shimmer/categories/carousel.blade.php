@props(['count' => 0])

<div class="container mt-14 max-lg:px-8 max-sm:mt-5 max-sm:!px-0">
    <div class="relative">
        <div class="scrollbar-hide flex gap-10 overflow-auto max-sm:gap-4">
            @for ($i = 0;  $i < $count; $i++)
                <div class="grid min-w-[120px] grid-cols-1 justify-items-center gap-4 max-sm:min-w-[80px] max-sm:gap-1.5">
                    <div class="shimmer relative h-[110px] w-[110px] overflow-hidden rounded-full max-sm:h-[80px] max-sm:w-[80px]">
                        <img class="rounded-sm bg-[#F5F5F5]">
                    </div>

                    <p class="shimmer h-[27px] w-[90px] rounded-2xl"></p>
                </div>
            @endfor
        </div>

        <span
            class="shimmer absolute -left-10 top-9 flex h-[50px] w-[50px] rounded-full max-sm:hidden"
            role="presentation"
        ></span>

        <span
            class="shimmer absolute -right-6 top-9 flex h-[50px] w-[50px] rounded-full max-sm:hidden"
            role="presentation"
        ></span>
    </div>
</div>
