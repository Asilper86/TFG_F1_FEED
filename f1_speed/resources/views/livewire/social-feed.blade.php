<div wire:poll.15s class="py-8 bg-[#121418] min-h-screen font-sans">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        <!-- Header -->
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-[#2d3136]">
            <h1 class="text-xl font-bold tracking-wide text-white uppercase italic flex items-center gap-3">
                <span class="text-[#E10600] text-2xl font-black">/</span> AUTOFEED
            </h1>
        </div>

        <!-- Create Post -->
        <div class="mb-6">
            @livewire('create-post')
        </div>

        <!-- Feed Controls -->
        <div class="flex items-center gap-4 mb-6 border-b border-[#2d3136] pb-2">
            <button wire:click="setFeedType('global')"
                class="text-xs font-bold uppercase tracking-widest pb-2 border-b-2 transition-colors {{ $feedType === 'global' ? 'border-[#E10600] text-white' : 'border-transparent text-gray-500 hover:text-gray-300' }}">
                Global
            </button>
            <button wire:click="setFeedType('following')"
                class="text-xs font-bold uppercase tracking-widest pb-2 border-b-2 transition-colors {{ $feedType === 'following' ? 'border-[#E10600] text-white' : 'border-transparent text-gray-500 hover:text-gray-300' }}">
                Siguiendo
            </button>
        </div>

        <!-- Posts -->
        <div class="space-y-4">
            @forelse ($posts as $post)
                @livewire('post-item', ['post' => $post], key('post-'.$post->id))
            @empty
                <div class="text-center text-gray-500 py-10 italic text-sm uppercase tracking-widest bg-[#1B1D21] border border-[#2d3136] rounded-lg">
                    No hay publicaciones todavía...
                </div>
            @endforelse
        </div>
    </div>
</div>
