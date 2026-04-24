<div class="bg-[#1B1D21] border border-[#2d3136] rounded-md p-5 mb-4 shadow-sm transition-all hover:border-[#3FA9F5]/30">

    <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
            <img src="{{ $post->user->profile_photo_url }}" alt="{{ $post->user->name }}"
                class="w-10 h-10 rounded-full object-cover border border-[#2d3136]">

            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-3">
                <p class="text-sm font-bold text-white uppercase tracking-wide">{{ $post->user->name }}</p>

                @if (auth()->id() !== $post->user_id)
                    <button wire:click="toggleFollow"
                        class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded border transition-colors {{ $isFollowing ? 'border-[#2d3136] text-gray-500 hover:text-white hover:border-gray-500' : 'border-[#E10600] text-[#E10600] hover:bg-[#E10600] hover:text-white' }}">
                        {{ $isFollowing ? 'SIGUIENDO' : 'SEGUIR' }}
                    </button>
                @endif
            </div>
        </div>

        <div class="flex flex-col items-end gap-2">
            <p class="text-[10px] uppercase tracking-widest text-gray-500 font-bold text-right">
                {{ $post->created_at->diffForHumans() }}
            </p>

            @if (auth()->id() === $post->user_id)
                <button wire:click="deletePost"
                    wire:confirm="¿Estás seguro de que quieres eliminar este post? Esta acción no se puede deshacer."
                    class="text-gray-500 hover:text-[#E10600] transition-colors" title="Eliminar Post">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                        </path>
                    </svg>
                </button>
            @endif
        </div>
    </div>

    @if ($post->content)
        <p class="text-gray-300 text-sm mb-4 leading-relaxed whitespace-pre-line">
            {{ $post->content }}
        </p>
    @endif

    @if ($post->media_path)
        <div class="mb-4 rounded overflow-hidden border border-[#2d3136]">
            <img src="{{ asset('storage/' . $post->media_path) }}" alt="Imagen adjunta"
                class="w-full max-h-96 object-cover">
        </div>
    @endif

    @if ($post->lap)
        <div class="mb-4 bg-[#1B1D21] border border-[#2d3136] rounded-lg overflow-hidden relative group">
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#E10600]"></div>

            <div class="p-4 pl-5">
                <div class="flex justify-between items-start mb-2">
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#E10600]">
                        DATOS DE TELEMETRÍA
                    </p>
                    <p class="text-[10px] uppercase text-gray-500 font-bold">Circuito: <span
                            class="text-white">{{ $post->lap->session->track_id }}</span></p>
                </div>

                <div class="flex items-end justify-between">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-widest mb-1">Tiempo de Vuelta</p>
                        <p class="text-2xl font-black text-white font-mono">
                            {{ floor($post->lap->lap_time / 60) }}:{{ str_pad(number_format(fmod($post->lap->lap_time, 60), 3), 6, '0', STR_PAD_LEFT) }}
                        </p>
                    </div>

                    <a href="{{ route('dashboard') }}"
                        class="text-[9px] bg-[#23262A] hover:bg-[#2d3136] text-white border border-[#2d3136] font-bold uppercase tracking-widest px-3 py-1.5 rounded transition-colors">
                        Analizar Vuelta →
                    </a>
                </div>
            </div>
        </div>
    @endif

    <div class="flex items-center gap-6 mt-4 pt-4 border-t border-[#2d3136]">
        <button wire:click="toggleLike"
            class="flex items-center gap-2 transition-colors group {{ $hasLiked ? 'text-[#E10600]' : 'text-gray-400 hover:text-[#E10600]' }}">
            <svg class="w-5 h-5 group-hover:scale-110 transition-transform {{ $hasLiked ? 'fill-current' : '' }}"
                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                </path>
            </svg>
            <span class="text-[11px] font-bold uppercase tracking-widest">
                {{ $likesCount }} LIKES
            </span>
        </button>

        <button wire:click="toggleComments"
            class="flex items-center gap-2 transition-colors group {{ $showComments ? 'text-[#3FA9F5]' : 'text-gray-400 hover:text-[#3FA9F5]' }}">
            <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                </path>
            </svg>
            <span class="text-[11px] font-bold uppercase tracking-widest">{{ $commentsCount }} RESPUESTAS</span>
        </button>
    </div>

    @if ($showComments)
        <div class="mt-4 pt-4 border-t border-[#2d3136]">
            <form wire:submit.prevent="addComment" class="mb-6">
                <div class="flex gap-3">
                    <img src="{{ auth()->user()->profile_photo_url }}"
                        class="w-8 h-8 rounded-full border border-[#2d3136] object-cover">
                    <div class="flex-1">
                        <input type="text" wire:model="newComment" placeholder="Postea tu respuesta..."
                            class="w-full bg-[#1B1D21] border-b border-transparent focus:border-[#E10600] text-white text-sm placeholder-gray-500 py-1 px-0 shadow-none focus:ring-0 transition-colors">
                        @error('newComment')
                            <span class="text-[#E10600] text-xs font-bold block">{{ $message }}</span>
                        @enderror
                        @error('commentMedia')
                            <span class="text-[#E10600] text-xs font-bold block">{{ $message }}</span>
                        @enderror

                        <!-- Previsualización de la foto del comentario -->
                        @if ($commentMedia)
                            <div class="mt-2 relative inline-block">
                                <img src="{{ $commentMedia->temporaryUrl() }}"
                                    class="h-20 rounded border border-[#2d3136] object-cover">
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex justify-between items-center mt-2 pl-11">
                    <div>
                        <input type="file" id="commentMediaUpload-{{ $post->id }}" wire:model="commentMedia"
                            class="hidden" accept="image/*">
                        <label for="commentMediaUpload-{{ $post->id }}"
                            class="cursor-pointer text-gray-500 hover:text-[#E10600] transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </label>
                    </div>
                    <button type="submit"
                        class="bg-[#E10600] text-white font-bold text-[10px] uppercase tracking-widest px-4 py-1.5 rounded hover:bg-[#ff0700] transition-colors disabled:opacity-50"
                        wire:loading.attr="disabled" wire:target="addComment">
                        Responder
                    </button>
                </div>
            </form>

            <div class="space-y-4">
                @foreach ($post->comments()->latest()->get() as $comment)
                    @livewire('comment-item', ['comment' => $comment], key('comment-'.$comment->id))
                @endforeach
            </div>
        </div>
    @endif

</div>
