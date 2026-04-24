<div>


    <div class="py-12 bg-[#1B1D21] min-h-screen font-sans">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="flex items-center gap-3 mb-8 pb-4 border-b border-[#2d3136]">
                <h1 class="text-[22px] font-bold tracking-wide text-white uppercase flex items-center gap-3">
                    <span class="text-[#E10600] text-3xl font-black">/</span> RED SOCIAL F1
                </h1>
            </div>

            <div class="mb-8">
                @livewire('create-post')
            </div>

            
            <div class="bg-[#23262A] border border-[#2d3136] overflow-hidden rounded p-6 text-white shadow-xl">

                <div class="flex items-center gap-6 mb-6 border-b border-[#2d3136] pb-3">
                    <button wire:click="setFeedType('global')"
                        class="text-[11px] font-bold uppercase tracking-widest flex items-center gap-2 transition-colors {{ $feedType === 'global' ? 'text-white' : 'text-gray-500 hover:text-gray-300' }}">
                        @if ($feedType === 'global')
                            <span class="w-2 h-2 bg-[#E10600] rounded-full"></span>
                        @endif
                        Global
                    </button>

                    <button wire:click="setFeedType('following')"
                        class="text-[11px] font-bold uppercase tracking-widest flex items-center gap-2 transition-colors {{ $feedType === 'following' ? 'text-white' : 'text-gray-500 hover:text-gray-300' }}">
                        @if ($feedType === 'following')
                            <span class="w-2 h-2 bg-[#E10600] rounded-full"></span>
                        @endif
                        Siguiendo
                    </button>
                </div>

                @if ($posts->isEmpty())
                    <div class="text-center text-gray-500 py-10 italic text-[12px] uppercase tracking-widest">
                        No hay publicaciones todavía... ¡Sé el primero en compartir algo!
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($posts as $post)
                            @livewire('post-item', ['post' => $post], key($post->id))
                        @endforeach
                    </div>
                @endif

            </div>



        </div>
    </div>



</div>
