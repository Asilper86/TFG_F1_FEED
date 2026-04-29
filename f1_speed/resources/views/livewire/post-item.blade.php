<div
    class="relative bg-[#1B1D21] border border-[#2d3136] rounded-md mb-4 shadow-sm transition-all hover:border-[#3FA9F5]/30 overflow-hidden">
    @if ($post->original_post_id)
        <div class="px-5 pt-3 flex items-center gap-2 text-gray-400">
            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="m17 2 4 4-4 4" />
                <path d="M3 11v-1a4 4 0 0 1 4-4h14" />
                <path d="m7 22-4-4 4-4" />
                <path d="M21 13v1a4 4 0 0 1-4 4H3" />
            </svg>
            <span class="text-[9px] font-bold uppercase tracking-[0.1em] italic">
                {{ $post->user_id === auth()->id() ? 'Reposteaste' : $post->user->name . ' ha compartido esto' }}
            </span>
        </div>
    @endif

    @php
        $displayPost = $post->original_post_id ? $post->originalPost : $post;
    <div class="flex flex-col sm:flex-row items-center sm:items-start justify-between gap-3 p-4 sm:p-5">
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <img src="{{ $displayPost->user->profile_photo_url }}" alt="{{ $displayPost->user->name }}"
                class="w-10 h-10 rounded-full object-cover border border-[#2d3136]">

            <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-3">
                <p class="text-sm font-bold text-white uppercase tracking-wide">{{ $displayPost->user->name }}</p>


                    @if (auth()->id() !== $displayPost->user_id)
                        <button wire:click="toggleFollow"
                            class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded border transition-colors {{ $isFollowing ? 'border-[#2d3136] text-gray-500 hover:text-white hover:border-gray-500' : 'border-[#E10600] text-[#E10600] hover:bg-[#E10600] hover:text-white' }}">
                            {{ $isFollowing ? 'SIGUIENDO' : 'SEGUIR' }}
                        </button>
                    @endif
                </div>
            </div>

            <div class="flex flex-col items-end gap-2">
                <p class="text-[10px] uppercase tracking-widest text-gray-500 font-bold text-right">
                    {{ $displayPost->created_at->diffForHumans() }}
                </p>

                @if ((int) auth()->id() === (int) $post->user_id)
                    <button
                        type="button"
                        wire:click="deletePost"
                        wire:confirm="¿Seguro que quieres borrar este post?"
                        class="text-gray-500 hover:text-[#E10600] transition-colors p-2"
                        title="Eliminar Post">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                    </button>
                @endif
            </div>
        </div>

        @if ($displayPost->content)
            <p class="text-gray-300 text-sm mb-4 leading-relaxed whitespace-pre-line">
                {!! App\Helpers\TextHelper::parseHashtags($displayPost->content) !!}
            </p>
        @endif

        @if ($displayPost->media_path)
            <div class="mb-4 rounded overflow-hidden border border-[#2d3136]">
                <img src="{{ asset('storage/' . $displayPost->media_path) }}" alt="Imagen adjunta"
                    class="w-full max-h-96 object-cover">
            </div>
        @endif

        @if ($displayPost->lap)
            @php
                $tracks = ['1' => 'Monza', '2' => 'Spa', '3' => 'Silverstone', '4' => 'Monaco', '5' => 'Barcelona'];
                $cars = ['1' => 'Ferrari', '2' => 'Red Bull', '3' => 'Mercedes', '4' => 'McLaren', '5' => 'Aston Martin'];
                $trackName = $tracks[$displayPost->lap->session->track_id] ?? 'Circuito';
                $carName = $cars[$displayPost->lap->session->car_id] ?? 'F1 Car';
            @endphp

            <div class="mb-4 bg-[#121418] border border-[#2d3136] rounded-lg overflow-hidden relative group">
                <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#E10600]"></div>
                
                <div class="p-4 pl-5 flex items-center justify-between">
                    <div>
                        <p class="text-[9px] font-black uppercase tracking-[0.2em] text-[#E10600] mb-1">DATA LOG: {{ $trackName }}</p>
                        <div class="flex items-baseline gap-3">
                            <p class="text-xl font-black text-white font-mono">
                                {{ floor($displayPost->lap->lap_time / 60) }}:{{ str_pad(number_format(fmod($displayPost->lap->lap_time, 60), 3), 6, '0', STR_PAD_LEFT) }}
                            </p>
                            <span class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">{{ $carName }}</span>
                        </div>
                    </div>
                    
                    <a href="{{ route('dashboard') }}" class="bg-[#1B1D21] hover:bg-[#2d3136] text-white text-[9px] font-bold uppercase tracking-widest px-4 py-2 rounded border border-[#2d3136] transition-all">
                        Ver Telemetría →
                    </a>
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

            <button wire:click="repost"
                class="flex items-center gap-2 transition-colors group {{ $hasReposted ? 'text-[#00D100]' : 'text-gray-400 hover:text-[#00D100]' }}">

                <svg class="w-5 h-5 group-hover:scale-110 transition-transform" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m17 2 4 4-4 4" />
                    <path d="M3 11v-1a4 4 0 0 1 4-4h14" />
                    <path d="m7 22-4-4 4-4" />
                    <path d="M21 13v1a4 4 0 0 1-4 4H3" />
                </svg>
                <span class="text-[11px] font-bold uppercase tracking-widest">
                    {{ $hasReposted ? 'REPOSTEASTE' : 'REPOST' }}
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
            @php $displayPost = $post->original_post_id ? $post->originalPost : $post; @endphp
            <div class="mt-6 space-y-4 border-t border-[#2d3136] pt-6">
                @foreach ($displayPost->comments as $comment)
                    @livewire('comment-item', ['comment' => $comment], key('comment-' . $comment->id))
                @endforeach

                <div class="mt-6 flex gap-3">
                    @if (auth()->user()->profile_photo_url)
                        <img src="{{ auth()->user()->profile_photo_url }}" class="w-8 h-8 rounded-full object-cover">
                    @else
                        <div class="w-8 h-8 rounded-full bg-[#E10600] flex items-center justify-center text-white font-bold text-xs">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                    @endif
                    <div class="flex-1">
                        <textarea wire:model="newComment"
                            class="w-full bg-[#121418] border border-[#2d3136] rounded-lg text-sm text-white placeholder-gray-600 focus:ring-[#E10600] focus:border-[#E10600] resize-none p-3"
                            placeholder="Añade un comentario..." rows="2"></textarea>

                        @if ($commentMedia)
                            <div class="mt-2 relative inline-block">
                                <img src="{{ $commentMedia->temporaryUrl() }}" class="w-20 h-20 object-cover rounded-lg">
                                <button wire:click="$set('commentMedia', null)"
                                    class="absolute -top-2 -right-2 bg-black/50 text-white rounded-full p-1 hover:bg-black transition-colors">
                                    <i class="fa-solid fa-xmark text-[10px]"></i>
                                </button>
                            </div>
                        @endif

                        <div class="flex justify-between items-center mt-3">
                            <label class="cursor-pointer text-gray-400 hover:text-white transition-colors">
                                <i class="fa-solid fa-image"></i>
                                <input type="file" wire:model="commentMedia" class="hidden" accept="image/*">
                            </label>
                            <button wire:click="addComment"
                                class="bg-[#E10600] text-white text-[10px] font-black uppercase tracking-widest px-4 py-2 rounded hover:bg-[#ff0700] transition-all active:scale-95">
                                Comentar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
