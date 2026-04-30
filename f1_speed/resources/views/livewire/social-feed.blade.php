<div wire:poll.15s >
    <div class="py-6 sm:py-12 bg-[#121418] min-h-screen font-sans">
        <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3 mb-6 sm:mb-8 pb-4 border-b border-[#2d3136]">
                <h1 class="text-xl sm:text-[22px] font-bold tracking-wide text-white uppercase flex items-center gap-3">
                    <span class="text-[#E10600] text-3xl font-black">/</span> AUTOFEED
                </h1>
            </div>

            <div class="mb-6 sm:mb-8">
                @livewire('create-post')
            </div>

            <div class="bg-[#23262A] border border-[#2d3136] overflow-hidden rounded shadow-xl">
                <div class="flex items-center gap-6 p-4 sm:p-6 border-b border-[#2d3136] bg-[#1B1D21]">
                    <button wire:click="setFeedType('global')"
                        class="text-[10px] sm:text-[11px] font-black uppercase tracking-[0.2em] flex items-center gap-2 transition-all {{ $feedType === 'global' ? 'text-[#E10600]' : 'text-gray-400 hover:text-white' }}">
                        GLOBAL
                    </button>

                    <button wire:click="setFeedType('following')"
                        class="text-[10px] sm:text-[11px] font-black uppercase tracking-[0.2em] flex items-center gap-2 transition-all {{ $feedType === 'following' ? 'text-[#E10600]' : 'text-gray-400 hover:text-white' }}">
                        SIGUIENDO
                    </button>
                </div>

                <div class="p-4 sm:p-6">

                @if ($posts->isEmpty())
                    <div class="text-center text-gray-500 py-10 italic text-[12px] uppercase tracking-widest">
                        No hay publicaciones todavía... ¡Sé el primero en compartir algo!
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($posts as $post)
                            @livewire('post-item', ['post' => $post], key('post-'.$post->id))
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
