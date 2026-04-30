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

    <div class="group bg-[#16181D] border border-white/5 rounded-2xl overflow-hidden hover:border-white/10 transition-all duration-300 shadow-2xl">
        <!-- Indicador de Repost -->
        @if ($post->original_post_id)
            <div class="px-6 py-2 bg-white/5 border-b border-white/5 flex items-center gap-2">
                <i class="fa-solid fa-retweet text-[#00D100] text-[10px]"></i>
                <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">
                    {{ $post->user->name }} ha reposteado
                </span>
            </div>
        @endif

        <div class="p-5 sm:p-6">
            <!-- Header: Avatar e Info -->
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center gap-4">
                    <a href="/profile/{{ $displayPost->user->id }}" class="relative group">
                        <img src="{{ $displayPost->user->profile_photo_url }}" 
                            class="w-12 h-12 rounded-full object-cover border-2 border-transparent group-hover:border-[#E10600] transition-all duration-300 shadow-lg">
                        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-[#E10600] rounded-full border-2 border-[#16181D] flex items-center justify-center">
                            <i class="fa-solid fa-check text-[6px] text-white"></i>
                        </div>
                    </a>
                    <div>
                        <div class="flex items-center gap-2">
                            <a href="/profile/{{ $displayPost->user->id }}" class="text-base font-black italic uppercase tracking-tighter text-white hover:text-[#E10600] transition-colors">
                                {{ $displayPost->user->name }}
                            </a>
                            <span class="text-gray-600 font-bold text-xs uppercase tracking-widest">@pilot_{{ $displayPost->user->id }}</span>
                        </div>
                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-[0.2em] mt-0.5">
                            <i class="fa-regular fa-clock mr-1"></i> {{ $displayPost->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>

                @if ((int) auth()->id() === (int) $post->user_id)
                    <button wire:click="deletePost" wire:confirm="¿Confirmar retirada de post?" class="w-8 h-8 rounded-full flex items-center justify-center text-gray-600 hover:bg-red-500/10 hover:text-red-500 transition-all">
                        <i class="fa-solid fa-trash-can text-sm"></i>
                    </button>
                @endif
            </div>

            <!-- Cuerpo del Post -->
            @if ($displayPost->content)
                <div class="text-[15px] text-gray-200 mb-5 leading-relaxed font-medium">
                    {!! App\Helpers\TextHelper::parseHashtags($displayPost->content) !!}
                </div>
            @endif

            <!-- Media -->
            @if ($displayPost->media_path)
                <div class="mb-5 rounded-2xl overflow-hidden border border-white/5 shadow-inner">
                    <img src="{{ asset('storage/' . $displayPost->media_path) }}" class="w-full max-h-[500px] object-cover hover:scale-[1.02] transition-transform duration-500">
                </div>
            @endif

            <!-- Telemetría Widget -->
            @if ($displayPost->lap)
                @php
                    $tracks = ['1' => 'Monza', '2' => 'Spa', '3' => 'Silverstone', '4' => 'Monaco', '5' => 'Barcelona'];
                    $trackName = $tracks[$displayPost->lap->session->track_id] ?? 'Circuit';
                @endphp
                <div class="mb-5 bg-[#0B0C0E] border border-white/5 rounded-2xl p-5 flex items-center justify-between relative overflow-hidden group/telemetry cursor-pointer" onclick="window.location='{{ route('dashboard') }}'">
                    <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-[#E10600] shadow-[0_0_15px_rgba(225,6,0,0.5)]"></div>
                    <div>
                        <p class="text-[10px] font-black text-[#E10600] uppercase tracking-[0.3em] mb-2 flex items-center gap-2">
                            <i class="fa-solid fa-gauge-high"></i> {{ $trackName }} Telemetry
                        </p>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-black text-white font-mono tracking-tighter italic">
                                {{ floor($displayPost->lap->lap_time / 60) }}:{{ str_pad(number_format(fmod($displayPost->lap->lap_time, 60), 3), 6, '0', STR_PAD_LEFT) }}
                            </span>
                            <span class="text-[10px] text-gray-500 font-bold uppercase">Lap Time</span>
                        </div>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-white/5 flex items-center justify-center group-hover/telemetry:bg-[#E10600] transition-all duration-300">
                        <i class="fa-solid fa-chart-line text-white text-lg"></i>
                    </div>
                </div>
            @endif

            <!-- Acciones Footer -->
            <div class="flex items-center gap-10 pt-4 border-t border-white/5">
                <button wire:click="toggleLike" class="flex items-center gap-2.5 transition-all group/btn {{ $hasLiked ? 'text-[#E10600]' : 'text-gray-500 hover:text-white' }}">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center group-hover/btn:bg-red-500/10 transition-all">
                        <i class="fa-{{ $hasLiked ? 'solid' : 'regular' }} fa-heart text-lg"></i>
                    </div>
                    <span class="text-xs font-black uppercase tracking-widest">{{ $likesCount }}</span>
                </button>

                <button wire:click="repost" class="flex items-center gap-2.5 transition-all group/btn {{ $hasReposted ? 'text-[#00D100]' : 'text-gray-500 hover:text-white' }}">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center group-hover/btn:bg-green-500/10 transition-all">
                        <i class="fa-solid fa-retweet text-lg"></i>
                    </div>
                    <span class="text-xs font-black uppercase tracking-widest">{{ $hasReposted ? 'Enviado' : 'Repost' }}</span>
                </button>

                <button wire:click="toggleComments" class="flex items-center gap-2.5 transition-all group/btn {{ $showComments ? 'text-[#3FA9F5]' : 'text-gray-500 hover:text-white' }}">
                    <div class="w-9 h-9 rounded-full flex items-center justify-center group-hover/btn:bg-blue-500/10 transition-all">
                        <i class="fa-regular fa-comment text-lg"></i>
                    </div>
                    <span class="text-xs font-black uppercase tracking-widest">{{ $commentsCount }}</span>
                </button>
            </div>

            <!-- Comentarios Dropdown -->
            @if ($showComments)
                <div class="mt-6 pt-6 border-t border-white/5 space-y-5 animate-in fade-in slide-in-from-top-4 duration-300">
                    @foreach ($displayPost->comments as $comment)
                        @livewire('comment-item', ['comment' => $comment], key('comment-'.$comment->id))
                    @endforeach
                    <div class="flex gap-4 items-center bg-[#0B0C0E] p-3 rounded-2xl border border-white/5">
                        <textarea wire:model="newComment" class="flex-1 bg-transparent border-none text-sm text-white placeholder-gray-600 focus:ring-0 resize-none py-1" placeholder="Añade tu respuesta..." rows="1"></textarea>
                        <button wire:click="addComment" class="bg-[#E10600] hover:bg-[#ff0700] text-white text-[10px] font-black uppercase tracking-widest px-5 py-2 rounded-xl transition-all">
                            Enviar
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div></div>
</div>
