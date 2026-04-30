<div wire:poll.15s >
    <div class="py-4 sm:py-8 bg-[#121418] min-h-screen font-sans">
        <div class="max-w-2xl mx-auto px-0 sm:px-4">
            <!-- Header F1 Estilo X -->
            <div class="px-4 py-3 sticky top-0 bg-[#121418]/80 backdrop-blur-md z-10 border-b border-[#2d3136] mb-4">
                <h1 class="text-lg font-black tracking-tight text-white uppercase italic">
                    <span class="text-[#E10600]">/</span> AUTOFEED
                </h1>
            </div>

            <!-- Caja de creación -->
            <div class="px-2 sm:px-0">
                @livewire('create-post')
            </div>

            <!-- Feed de Posts -->
            <div class="divide-y divide-[#2d3136] border-t border-[#2d3136]">
                @forelse ($posts as $post)
                    <div class="bg-[#121418] hover:bg-[#16181d] transition-colors border-b border-[#2d3136]">
                        @livewire('post-item', ['post' => $post], key('post-'.$post->id))
                    </div>
                @empty
                    <div class="text-center text-gray-500 py-20 italic text-[13px] uppercase tracking-widest">
                        No hay telemetría compartida aún...
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
