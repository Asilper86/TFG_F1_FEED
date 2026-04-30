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

    <div class="bg-[#1B1D21] border border-white/5 rounded-xl hover:border-white/10 transition-all duration-200 shadow-lg">
        <!-- Repost Header -->
        @if ($post->original_post_id)
            <div class="px-4 py-1.5 border-b border-white/5 bg-white/[0.02] flex items-center gap-2">
                <i class="fa-solid fa-retweet text-green-500 text-[10px]"></i>
                <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ $post->user->name }} ha compartido</span>
            </div>
        @endif

        <div class="p-4 flex gap-4">
            <!-- Left: Avatar -->
            <div class="shrink-0">
                <a href="/profile/{{ $displayPost->user->id }}">
                    <img src="{{ $displayPost->user->profile_photo_url }}" class="w-10 h-10 sm:w-11 sm:h-11 rounded-full object-cover border border-white/10 shadow-sm">
                </a>
            </div>

            <!-- Right: Content -->
            <div class="flex-1 min-w-0">
                <!-- Top Row: Name and Time -->
                <div class="flex items-center justify-between mb-1">
                    <div class="flex items-center gap-2 min-w-0">
                        <a href="/profile/{{ $displayPost->user->id }}" class="text-[14px] font-black uppercase italic text-white hover:text-[#E10600] transition-colors truncate">
                            {{ $displayPost->user->name }}
                        </a>
                        <span class="text-gray-600 text-[11px] font-bold truncate">@pilot_{{ $displayPost->user->id }}</span>
                        <span class="text-gray-700 text-[11px] font-bold">·</span>
                        <span class="text-gray-500 text-[11px] font-medium whitespace-nowrap">{{ $displayPost->created_at->diffForHumans(short: true) }}</span>
                    </div>

                    @if ((int) auth()->id() === (int) $post->user_id)
                        <button wire:click="deletePost" wire:confirm="¿Borrar post?" class="text-gray-700 hover:text-red-500 transition-colors p-1">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                    @endif
                </div>

                <!-- Message -->
                @if ($displayPost->content)
                    <div class="text-[14px] text-gray-200 leading-snug mb-3 break-words font-medium">
                        {!! App\Helpers\TextHelper::parseHashtags($displayPost->content) !!}
                    </div>
                @endif

                <!-- Image -->
                @if ($displayPost->media_path)
                    <div class="mb-3 rounded-lg overflow-hidden border border-white/5">
                        <img src="{{ asset('storage/' . $displayPost->media_path) }}" class="w-full max-h-[400px] object-cover">
                    </div>
                @endif

                <!-- Telemetry Card (Compact) -->
                @if ($displayPost->lap)
                    @php
                        $tracks = ['1' => 'Monza', '2' => 'Spa', '3' => 'Silverstone', '4' => 'Monaco', '5' => 'Barcelona'];
                        $trackName = $tracks[$displayPost->lap->session->track_id] ?? 'Circuit';
                    @endphp
                    <div class="mb-3 bg-[#121418] border-l-2 border-[#E10600] rounded-r-lg p-3 flex items-center justify-between group cursor-pointer hover:bg-[#16181d] transition-all" onclick="window.location='{{ route('dashboard') }}'">
                        <div>
                            <p class="text-[9px] font-black text-[#E10600] uppercase tracking-widest mb-0.5">{{ $trackName }} LAP</p>
                            <p class="text-base font-black text-white font-mono italic">
                                {{ floor($displayPost->lap->lap_time / 60) }}:{{ str_pad(number_format(fmod($displayPost->lap->lap_time, 60), 3), 6, '0', STR_PAD_LEFT) }}
                            </p>
                        </div>
                        <i class="fa-solid fa-chevron-right text-gray-800 group-hover:text-white transition-colors text-[10px]"></i>
                    </div>
                @endif

                <!-- Action Bar (Icons) -->
                <div class="flex items-center gap-6 pt-1 text-gray-500">
                    <button wire:click="toggleLike" class="flex items-center gap-1.5 transition-colors group {{ $hasLiked ? 'text-red-500' : 'hover:text-red-500' }}">
                        <i class="fa-{{ $hasLiked ? 'solid' : 'regular' }} fa-heart text-[14px]"></i>
                        <span class="text-[11px] font-bold">{{ $likesCount }}</span>
                    </button>

                    <button wire:click="repost" class="flex items-center gap-1.5 transition-colors group {{ $hasReposted ? 'text-green-500' : 'hover:text-green-500' }}">
                        <i class="fa-solid fa-retweet text-[14px]"></i>
                        <span class="text-[11px] font-bold">{{ $hasReposted ? '1' : '' }}</span>
                    </button>

                    <button wire:click="toggleComments" class="flex items-center gap-1.5 transition-colors group hover:text-blue-400">
                        <i class="fa-regular fa-comment text-[14px]"></i>
                        <span class="text-[11px] font-bold">{{ $commentsCount }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Comments Section -->
        @if ($showComments)
            <div class="px-4 pb-4 space-y-4 border-t border-white/5 pt-4 bg-white/[0.01]">
                @foreach ($displayPost->comments as $comment)
                    @livewire('comment-item', ['comment' => $comment], key('comment-'.$comment->id))
                @endforeach
                <div class="flex gap-2">
                    <input wire:model="newComment" class="flex-1 bg-[#121418] border border-white/5 rounded-lg text-xs text-white px-3 py-1.5 outline-none focus:border-[#E10600]" placeholder="Añade tu respuesta...">
                    <button wire:click="addComment" class="bg-[#E10600] hover:bg-red-600 text-white text-[10px] font-black uppercase px-4 py-1.5 rounded-lg transition-all">Enviar</button>
                </div>
            </div>
        @endif
    </div></div>
</div>
