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
    @endphp

    <div class="p-4 sm:p-6 bg-[#1B1D21] border border-[#2d3136] rounded-lg shadow-sm hover:border-[#E10600]/30 transition-all">
        <!-- Encabezado -->
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <a href="/profile/{{ $displayPost->user->id }}">
                    <img src="{{ $displayPost->user->profile_photo_url }}" class="w-10 h-10 rounded-full border border-[#2d3136] object-cover">
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <a href="/profile/{{ $displayPost->user->id }}" class="text-sm font-black uppercase tracking-wide text-white hover:text-[#E10600] transition-colors">
                            {{ $displayPost->user->name }}
                        </a>
                        @if (auth()->id() !== $displayPost->user_id)
                            <button wire:click="toggleFollow" class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded border {{ $isFollowing ? 'border-[#2d3136] text-gray-500' : 'border-[#E10600] text-[#E10600]' }}">
                                {{ $isFollowing ? 'SIGUIENDO' : 'SEGUIR' }}
                            </button>
                        @endif
                    </div>
                    <p class="text-[10px] text-gray-500 uppercase font-bold tracking-widest">@pilot_{{ $displayPost->user->id }} · {{ $displayPost->created_at->diffForHumans() }}</p>
                </div>
            </div>

            @if ((int) auth()->id() === (int) $post->user_id)
                <button wire:click="deletePost" wire:confirm="¿Borrar?" class="text-gray-600 hover:text-[#E10600] transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            @endif
        </div>

        <!-- Contenido -->
        @if ($displayPost->content)
            <div class="text-sm sm:text-base text-gray-200 mb-4 leading-relaxed">
                {!! App\Helpers\TextHelper::parseHashtags($displayPost->content) !!}
            </div>
        @endif

        @if ($displayPost->media_path)
            <div class="mb-4 rounded-lg overflow-hidden border border-[#2d3136]">
                <img src="{{ asset('storage/' . $displayPost->media_path) }}" class="w-full max-h-96 object-cover">
            </div>
        @endif

        <!-- Telemetría (Card F1) -->
        @if ($displayPost->lap)
            @php
                $tracks = ['1' => 'Monza', '2' => 'Spa', '3' => 'Silverstone', '4' => 'Monaco', '5' => 'Barcelona'];
                $trackName = $tracks[$displayPost->lap->session->track_id] ?? 'Circuito';
            @endphp
            <div class="mb-4 bg-[#121418] border-l-4 border-[#E10600] rounded p-4 flex items-center justify-between group cursor-pointer hover:bg-[#16181d] transition-all" onclick="window.location='{{ route('dashboard') }}'">
                <div>
                    <p class="text-[9px] font-black text-[#E10600] uppercase tracking-widest mb-1">{{ $trackName }} DATA LOG</p>
                    <p class="text-xl font-black text-white font-mono">
                        {{ floor($displayPost->lap->lap_time / 60) }}:{{ str_pad(number_format(fmod($displayPost->lap->lap_time, 60), 3), 6, '0', STR_PAD_LEFT) }}
                    </p>
                </div>
                <div class="text-right">
                    <span class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">Ver Análisis →</span>
                </div>
            </div>
        @endif

        <!-- Acciones -->
        <div class="flex items-center gap-6 pt-4 border-t border-[#2d3136]">
            <button wire:click="toggleLike" class="flex items-center gap-2 group {{ $hasLiked ? 'text-[#E10600]' : 'text-gray-500 hover:text-[#E10600]' }}">
                <svg class="w-5 h-5 transition-transform group-hover:scale-110 {{ $hasLiked ? 'fill-current' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                <span class="text-xs font-bold">{{ $likesCount }}</span>
            </button>

            <button wire:click="toggleComments" class="flex items-center gap-2 group {{ $showComments ? 'text-[#3FA9F5]' : 'text-gray-500 hover:text-[#3FA9F5]' }}">
                <svg class="w-5 h-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                <span class="text-xs font-bold">{{ $commentsCount }}</span>
            </button>
        </div>

        @if ($showComments)
            <div class="mt-4 pt-4 border-t border-[#2d3136] space-y-4">
                @foreach ($displayPost->comments as $comment)
                    @livewire('comment-item', ['comment' => $comment], key('comment-'.$comment->id))
                @endforeach
                <div class="flex gap-2">
                    <textarea wire:model="newComment" class="flex-1 bg-[#121418] border border-[#2d3136] rounded-lg text-sm text-white p-2 resize-none outline-none focus:ring-1 focus:ring-[#E10600]" placeholder="Escribe un comentario..." rows="1"></textarea>
                    <button wire:click="addComment" class="bg-[#E10600] text-white text-[10px] font-black uppercase px-4 py-1 rounded">Enviar</button>
                </div>
            </div>
        @endif
    </div>
</div>
