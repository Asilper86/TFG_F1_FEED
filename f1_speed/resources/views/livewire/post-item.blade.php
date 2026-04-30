    @php
        $displayPost = $post->original_post_id ? $post->originalPost : $post;
    @endphp

<div class="bg-[#1B1D21] border border-[#2d3136] rounded-lg shadow-md mb-4 overflow-hidden">
    <!-- Repost Header -->
    @if ($post->original_post_id)
        <div class="px-4 py-2 bg-[#23262A] border-b border-[#2d3136] flex items-center gap-2">
            <svg class="w-4 h-4 text-[#00D100]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
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
                <div class="flex items-center flex-wrap gap-2">
                    <a href="/profile/{{ $displayPost->user->id }}" class="text-sm font-bold text-white hover:underline truncate">
                        {{ $displayPost->user->name }}
                    </a>
                    @if ((int) auth()->id() !== (int) $displayPost->user_id)
                        <button wire:click="toggleFollow" class="text-[9px] font-black uppercase tracking-widest px-2 py-0.5 rounded border transition-colors {{ $isFollowing ? 'border-[#2d3136] text-gray-500 hover:text-white' : 'border-[#E10600] text-[#E10600] hover:bg-[#E10600] hover:text-white' }}">
                            {{ $isFollowing ? 'Siguiendo' : 'Seguir' }}
                        </button>
                    @endif
                    <span class="text-gray-500 text-xs truncate">@pilot_{{ $displayPost->user->id }}</span>
                    <span class="text-gray-600 text-xs">·</span>
                    <span class="text-gray-500 text-xs whitespace-nowrap">{{ $displayPost->created_at->diffForHumans(short: true) }}</span>
                </div>

                @if ((int) auth()->id() === (int) $post->user_id)
                    <button wire:click="deletePost" wire:confirm="¿Borrar post?" class="text-gray-500 hover:text-[#E10600] transition-colors p-1 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                @endif
            </div>

            <!-- Content -->
            @if ($displayPost->content)
                <div class="text-sm text-white leading-relaxed mb-3 whitespace-pre-line break-words">
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
                            <span class="text-[10px] text-gray-500 uppercase font-bold flex items-center justify-end">Ver Datos <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="flex items-center gap-8 mt-3 pt-3 border-t border-[#2d3136] text-gray-400">
                <button wire:click="toggleLike" class="flex items-center gap-2 transition-colors hover:text-[#E10600] {{ $hasLiked ? 'text-[#E10600]' : '' }}">
                    <svg class="w-5 h-5 {{ $hasLiked ? 'fill-current' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    <span class="text-xs font-bold">{{ $likesCount > 0 ? $likesCount : '' }}</span>
                </button>

                <button wire:click="repost" class="flex items-center gap-2 transition-colors hover:text-[#00D100] {{ $hasReposted ? 'text-[#00D100]' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    <span class="text-xs font-bold">{{ $hasReposted ? '1' : '' }}</span>
                </button>

                <button wire:click="toggleComments" class="flex items-center gap-2 transition-colors hover:text-[#3FA9F5] {{ $showComments ? 'text-[#3FA9F5]' : '' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    <span class="text-xs font-bold">{{ $commentsCount > 0 ? $commentsCount : '' }}</span>
                </button>
            </div>
            
            <!-- Comments -->
            @if ($showComments)
                <div class="mt-4 pt-4 border-t border-[#2d3136] space-y-4">
                    @foreach ($displayPost->comments as $comment)
                        @livewire('comment-item', ['comment' => $comment], key('comment-'.$comment->id))
                    @endforeach
                    <div class="flex flex-col gap-2">
                        <div class="flex gap-2 items-center">
                            <input wire:model="newComment" type="text" class="flex-1 bg-[#121418] border border-[#2d3136] rounded text-sm text-white px-3 py-2 outline-none focus:border-[#E10600]" placeholder="Escribe tu respuesta...">
                            
                            <label class="cursor-pointer text-gray-500 hover:text-[#E10600] transition-colors p-2">
                                <input type="file" wire:model="commentMedia" class="hidden" accept="image/*,video/*">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </label>

                            <button wire:click="addComment" class="bg-[#E10600] hover:bg-red-700 text-white text-[11px] font-black uppercase px-4 py-2 rounded transition-colors">Responder</button>
                        </div>
                        @if ($commentMedia)
                            <div class="relative inline-block w-fit mt-1">
                                <img src="{{ $commentMedia->temporaryUrl() }}" class="h-16 rounded border border-[#2d3136] object-cover">
                                <button wire:click="$set('commentMedia', null)" class="absolute -top-2 -right-2 bg-[#E10600] text-white rounded-full w-4 h-4 flex items-center justify-center shadow-lg">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
