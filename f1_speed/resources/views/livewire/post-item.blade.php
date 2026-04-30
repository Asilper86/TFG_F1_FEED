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

    <div class="flex gap-3 p-3 sm:p-4">
        <!-- Avatar a la Izquierda -->
        <div class="flex-shrink-0">
            <a href="/profile/{{ $displayPost->user->id }}">
                <img src="{{ $displayPost->user->profile_photo_url }}" alt="{{ $displayPost->user->name }}"
                    class="w-10 h-10 sm:w-12 sm:h-12 rounded-full object-cover border border-[#2d3136] hover:opacity-90 transition-opacity">
            </a>
        </div>

        <!-- Contenido a la Derecha -->
        <div class="flex-1 min-w-0">
            <!-- Header: Nombre, Info y Borrar -->
            <div class="flex items-start justify-between gap-2 mb-1">
                <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 min-w-0">
                    <a href="/profile/{{ $displayPost->user->id }}" class="text-[14px] sm:text-[15px] font-bold text-white hover:underline truncate">
                        {{ $displayPost->user->name }}
                    </a>
                    <span class="text-gray-500 text-[13px] truncate">@pilot_{{ $displayPost->user->id }}</span>
                    <span class="text-gray-500 text-[13px]">·</span>
                    <span class="text-gray-500 text-[13px] whitespace-nowrap">{{ $displayPost->created_at->diffForHumans(short: true) }}</span>

                    @if (auth()->id() !== $displayPost->user_id)
                        <button wire:click="toggleFollow" class="ml-1 text-[11px] font-black text-[#E10600] hover:underline uppercase tracking-wider">
                            {{ $isFollowing ? 'Siguiendo' : 'Seguir' }}
                        </button>
                    @endif
                </div>

                @if ((int) auth()->id() === (int) $post->user_id)
                    <button wire:click="deletePost" wire:confirm="¿Borrar post?" class="text-gray-600 hover:text-[#E10600] transition-colors p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                @endif
            </div>

            <!-- Cuerpo del mensaje -->
            @if ($displayPost->content)
                <div class="text-[14px] sm:text-[15px] text-gray-200 leading-normal mb-3 whitespace-pre-line break-words">
                    {!! App\Helpers\TextHelper::parseHashtags($displayPost->content) !!}
                </div>
            @endif

            <!-- Imagen si existe -->
            @if ($displayPost->media_path)
                <div class="mb-3 rounded-xl overflow-hidden border border-[#2d3136]">
                    <img src="{{ asset('storage/' . $displayPost->media_path) }}" class="w-full max-h-[500px] object-cover">
                </div>
            @endif

            <!-- Telemetría (Estilo Twitter Card) -->
            @if ($displayPost->lap)
                @php
                    $tracks = ['1' => 'Monza', '2' => 'Spa', '3' => 'Silverstone', '4' => 'Monaco', '5' => 'Barcelona'];
                    $cars = ['1' => 'Ferrari', '2' => 'Red Bull', '3' => 'Mercedes', '4' => 'McLaren', '5' => 'Aston Martin'];
                    $trackName = $tracks[$displayPost->lap->session->track_id] ?? 'Circuito';
                    $carName = $cars[$displayPost->lap->session->car_id] ?? 'F1 Car';
                @endphp

                <div class="mb-3 bg-[#121418] border border-[#2d3136] rounded-xl overflow-hidden flex flex-col sm:flex-row hover:bg-[#16181d] transition-colors group cursor-pointer" onclick="window.location='{{ route('dashboard') }}'">
                    <div class="w-full sm:w-32 h-20 sm:h-auto bg-[#E10600] flex items-center justify-center shrink-0">
                        <i class="fa-solid fa-gauge-high text-white text-2xl"></i>
                    </div>
                    <div class="p-3 flex-1 flex flex-col justify-center min-w-0">
                        <p class="text-[10px] font-black text-[#E10600] uppercase tracking-widest">{{ $trackName }} · {{ $carName }}</p>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-lg font-black text-white font-mono">
                                {{ floor($displayPost->lap->lap_time / 60) }}:{{ str_pad(number_format(fmod($displayPost->lap->lap_time, 60), 3), 6, '0', STR_PAD_LEFT) }}
                            </span>
                            <span class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">LAP TIME</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Acciones (Like, Repost, Comentarios) -->
            <div class="flex items-center justify-between max-w-sm mt-3 text-gray-500">
                <button wire:click="toggleComments" class="flex items-center gap-2 group hover:text-[#3FA9F5] transition-colors">
                    <div class="p-2 rounded-full group-hover:bg-[#3FA9F5]/10 transition-colors">
                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <span class="text-[12px]">{{ $commentsCount }}</span>
                </button>

                <button wire:click="repost" class="flex items-center gap-2 group {{ $hasReposted ? 'text-[#00D100]' : 'hover:text-[#00D100]' }} transition-colors">
                    <div class="p-2 rounded-full group-hover:bg-[#00D100]/10 transition-colors">
                        <svg class="w-4.5 h-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m17 2 4 4-4 4" /><path d="M3 11v-1a4 4 0 0 1 4-4h14" /><path d="m7 22-4-4 4-4" /><path d="M21 13v1a4 4 0 0 1-4 4H3" /></svg>
                    </div>
                    <span class="text-[12px]">{{ $hasReposted ? '1' : '' }}</span>
                </button>

                <button wire:click="toggleLike" class="flex items-center gap-2 group {{ $hasLiked ? 'text-[#E10600]' : 'hover:text-[#E10600]' }} transition-colors">
                    <div class="p-2 rounded-full group-hover:bg-[#E10600]/10 transition-colors">
                        <svg class="w-4.5 h-4.5 {{ $hasLiked ? 'fill-current' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    </div>
                    <span class="text-[12px]">{{ $likesCount }}</span>
                </button>
            </div>

            <!-- Sección de Comentarios -->
            @if ($showComments)
                <div class="mt-4 space-y-4 border-t border-[#2d3136] pt-4">
                    @foreach ($displayPost->comments as $comment)
                        @livewire('comment-item', ['comment' => $comment], key('comment-' . $comment->id))
                    @endforeach
                    <div class="mt-4 flex gap-2">
                        <textarea wire:model="newComment" class="flex-1 bg-[#121418] border border-[#2d3136] rounded-xl text-sm text-white p-2.5 resize-none outline-none focus:ring-1 focus:ring-[#E10600]" placeholder="Postea tu respuesta" rows="1"></textarea>
                        <button wire:click="addComment" class="bg-[#E10600] text-white text-[12px] font-bold px-4 py-1 rounded-full hover:bg-[#ff0700] h-fit self-end mb-1">Responder</button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
