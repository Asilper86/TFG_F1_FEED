    @php
        $displayPost = $post->original_post_id ? $post->originalPost : $post;
    @endphp

<div class="bg-[#1B1D21] border border-[#2d3136] rounded-lg shadow-md mb-4 overflow-hidden">
    <!-- Repost Header -->
    @if ($post->original_post_id)
        <div class="px-4 py-2 bg-[#23262A] border-b border-[#2d3136] flex items-center gap-2">
            <i class="fa-solid fa-retweet text-[#00D100] text-[11px]"></i>
            <span class="text-[11px] font-bold text-gray-400 uppercase tracking-widest">{{ $post->user->name }} ha reposteado</span>
        </div>
    @endif

    <div class="p-4 sm:p-5 flex gap-3 sm:gap-4">
        <!-- Left: Avatar -->
        <div class="shrink-0">
            <a href="/profile/{{ $displayPost->user->id }}">
                <img src="{{ $displayPost->user->profile_photo_url }}" class="w-10 h-10 sm:w-12 sm:h-12 rounded-full border border-[#2d3136] object-cover">
            </a>
        </div>

        <!-- Right: Content -->
        <div class="flex-1 min-w-0">
            <!-- Header Row -->
            <div class="flex items-start justify-between mb-2">
                <div class="flex items-center flex-wrap gap-x-2 gap-y-1">
                    <a href="/profile/{{ $displayPost->user->id }}" class="text-sm font-bold text-white hover:underline truncate">
                        {{ $displayPost->user->name }}
                    </a>
                    <span class="text-gray-500 text-xs truncate">@pilot_{{ $displayPost->user->id }}</span>
                    <span class="text-gray-600 text-xs">·</span>
                    <span class="text-gray-500 text-xs whitespace-nowrap">{{ $displayPost->created_at->diffForHumans(short: true) }}</span>
                </div>

                @if ((int) auth()->id() === (int) $post->user_id)
                    <button wire:click="deletePost" wire:confirm="¿Borrar post?" class="text-gray-500 hover:text-[#E10600] transition-colors p-1 shrink-0">
                        <i class="fa-solid fa-trash-can text-sm"></i>
                    </button>
                @endif
            </div>

            <!-- Content -->
            @if ($displayPost->content)
                <div class="text-sm text-gray-200 leading-relaxed mb-3 whitespace-pre-line break-words">
                    {!! App\Helpers\TextHelper::parseHashtags($displayPost->content) !!}
                </div>
            @endif

            <!-- Media -->
            @if ($displayPost->media_path)
                <div class="mb-3 rounded border border-[#2d3136] overflow-hidden">
                    <img src="{{ asset('storage/' . $displayPost->media_path) }}" class="w-full max-h-96 object-cover">
                </div>
            @endif

            <!-- Telemetry -->
            @if ($displayPost->lap)
                @php
                    $tracks = ['1' => 'Monza', '2' => 'Spa', '3' => 'Silverstone', '4' => 'Monaco', '5' => 'Barcelona'];
                    $trackName = $tracks[$displayPost->lap->session->track_id] ?? 'Circuito';
                @endphp
                <div class="mb-3 bg-[#121418] border border-[#2d3136] rounded-md overflow-hidden flex items-stretch cursor-pointer hover:bg-[#16181d] transition-colors" onclick="window.location='{{ route('dashboard') }}'">
                    <div class="w-1.5 bg-[#E10600] shrink-0"></div>
                    <div class="p-3 flex-1 flex flex-col sm:flex-row sm:items-center justify-between gap-2 min-w-0">
                        <div>
                            <p class="text-[10px] font-black text-[#E10600] uppercase tracking-widest mb-1">{{ $trackName }}</p>
                            <p class="text-xl font-bold text-white font-mono leading-none">
                                {{ floor($displayPost->lap->lap_time / 60) }}:{{ str_pad(number_format(fmod($displayPost->lap->lap_time, 60), 3), 6, '0', STR_PAD_LEFT) }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] text-gray-500 uppercase font-bold">Ver Datos <i class="fa-solid fa-chevron-right ml-1"></i></span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="flex items-center gap-6 mt-3 pt-3 border-t border-[#2d3136] text-gray-400">
                <button wire:click="toggleLike" class="flex items-center gap-2 transition-colors hover:text-[#E10600] {{ $hasLiked ? 'text-[#E10600]' : '' }}">
                    <i class="fa-{{ $hasLiked ? 'solid' : 'regular' }} fa-heart text-[15px]"></i>
                    <span class="text-xs font-bold">{{ $likesCount }}</span>
                </button>

                <button wire:click="repost" class="flex items-center gap-2 transition-colors hover:text-[#00D100] {{ $hasReposted ? 'text-[#00D100]' : '' }}">
                    <i class="fa-solid fa-retweet text-[15px]"></i>
                    <span class="text-xs font-bold">{{ $hasReposted ? '1' : '' }}</span>
                </button>

                <button wire:click="toggleComments" class="flex items-center gap-2 transition-colors hover:text-[#3FA9F5] {{ $showComments ? 'text-[#3FA9F5]' : '' }}">
                    <i class="fa-regular fa-comment text-[15px]"></i>
                    <span class="text-xs font-bold">{{ $commentsCount }}</span>
                </button>
            </div>
            
            <!-- Comments -->
            @if ($showComments)
                <div class="mt-4 pt-4 border-t border-[#2d3136] space-y-4">
                    @foreach ($displayPost->comments as $comment)
                        @livewire('comment-item', ['comment' => $comment], key('comment-'.$comment->id))
                    @endforeach
                    <div class="flex gap-2">
                        <input wire:model="newComment" type="text" class="flex-1 bg-[#121418] border border-[#2d3136] rounded text-sm text-white px-3 py-2 outline-none focus:border-[#E10600]" placeholder="Escribe tu respuesta...">
                        <button wire:click="addComment" class="bg-[#E10600] hover:bg-red-700 text-white text-[11px] font-black uppercase px-4 py-2 rounded transition-colors">Responder</button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
