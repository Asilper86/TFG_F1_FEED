<div wire:poll.15s >
    <div class="py-6 sm:py-10 bg-[#0B0C0E] min-h-screen font-sans">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">
            <!-- Header Premium -->
            <div class="flex items-center justify-between mb-8 pb-4 border-b border-white/5">
                <div class="flex items-center gap-4">
                    <div class="w-1 h-8 bg-[#E10600] rounded-full"></div>
                    <h1 class="text-2xl font-black tracking-tighter text-white uppercase italic">
                        Autofeed <span class="text-[#E10600]/50 text-sm not-italic ml-2 tracking-widest font-bold">LIVE_DATA</span>
                    </h1>
                </div>
                <div class="flex bg-[#1B1D21] p-1 rounded-lg border border-white/5">
                    <button wire:click="setFeedType('global')"
                        class="px-4 py-1.5 rounded-md text-[10px] font-black uppercase tracking-widest transition-all {{ $feedType === 'global' ? 'bg-[#E10600] text-white shadow-lg' : 'text-gray-500 hover:text-white' }}">
                        Global
                    </button>
                    <button wire:click="setFeedType('following')"
                        class="px-4 py-1.5 rounded-md text-[10px] font-black uppercase tracking-widest transition-all {{ $feedType === 'following' ? 'bg-[#E10600] text-white shadow-lg' : 'text-gray-500 hover:text-white' }}">
                        Siguiendo
                    </button>
                </div>
            </div>

            <!-- Create Post Section -->
            <div class="mb-10">
                @livewire('create-post')
            </div>

            <!-- Posts Timeline -->
            <div class="space-y-6">
                @forelse ($posts as $post)
                    <div class="transform transition-all duration-300">
                        @livewire('post-item', ['post' => $post], key('post-'.$post->id))
                    </div>
                @empty
                    <div class="bg-[#1B1D21] rounded-2xl border border-white/5 p-20 text-center">
                        <i class="fa-solid fa-satellite-dish text-4xl text-gray-700 mb-4"></i>
                        <p class="text-gray-500 text-xs uppercase tracking-[0.3em] font-bold">Sin actividad en el sector</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
