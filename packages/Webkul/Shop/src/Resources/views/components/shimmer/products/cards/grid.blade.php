@props(['count' => 0])

@for ($i = 0;  $i < $count; $i++)
    <div class="grid gap-2.5 relative max-sm:grid-cols-1 w-full max-w-[291px] {{ $attributes["class"] }}">
        <div class="relative w-full rounded-sm shimmer">
            <div class="relative after:content-[' '] after:block after:pb-[calc(100%+9px)]"></div>
        </div>

        <div class="grid gap-2.5 content-start">
            <p class="w-[75%] h-[24px] shimmer"></p>
            <p class="w-[55%] h-[24px] shimmer"></p>

            {{-- color box shimmer --}}
            <div class="flex gap-4 mt-[12px]">
                <span class="rounded-full w-[30px] h-[30px] block shimmer"></span>
                <span class="rounded-full w-[30px] h-[30px] block shimmer"></span>
            </div>
        </div>
    </div>
@endfor
