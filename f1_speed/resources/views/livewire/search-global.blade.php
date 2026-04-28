<div class="py-12 bg-[#121418] min-h-screen text-white">
    <div class="max-w-4xl mx-auto px-4">
        
        <!-- Barra de Búsqueda Gigante -->
        <div class="relative mb-8">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="h-6 w-6 text-[#E10600]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text" wire:model.live.debounce.300ms="searchQuery" 
                class="block w-full pl-12 pr-4 py-4 bg-[#1B1D21] border border-[#2d3136] rounded-xl text-xl text-white placeholder-gray-500 focus:ring-2 focus:ring-[#E10600] focus:border-transparent shadow-2xl transition-all" 
                placeholder="Busca pilotos, posts o #hashtags...">
        </div>

        <!-- Filtros Rápidos -->
        <div class="flex gap-4 mb-8 overflow-x-auto pb-2">
            <button wire:click="$set('filter', 'all')" class="px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $filter === 'all' ? 'bg-[#E10600] border-[#E10600] text-white' : 'border-[#2d3136] text-gray-400 hover:border-gray-500' }}">Todos</button>
            <button wire:click="$set('filter', 'users')" class="px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $filter === 'users' ? 'bg-[#E10600] border-[#E10600] text-white' : 'border-[#2d3136] text-gray-400 hover:border-gray-500' }}">Pilotos</button>
            <button wire:click="$set('filter', 'posts')" class="px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $filter === 'posts' ? 'bg-[#E10600] border-[#E10600] text-white' : 'border-[#2d3136] text-gray-400 hover:border-gray-500' }}">Posts</button>
            <button wire:click="$set('filter', 'hashtags')" class="px-4 py-2 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $filter === 'hashtags' ? 'bg-[#E10600] border-[#E10600] text-white' : 'border-[#2d3136] text-gray-400 hover:border-gray-500' }}">Hashtags</button>
        </div>

        <!-- Resultados -->
        @if(strlen($searchQuery) < 2)
            <div class="text-center py-20 bg-[#1B1D21] border border-[#2d3136] rounded-xl">
                <p class="text-gray-500 text-sm uppercase tracking-widest font-bold">Escribe algo para empezar a buscar</p>
            </div>
        @else
            <div class="space-y-8 pb-20">
                
                {{-- Sección Pilotos --}}
                @if(count($users) > 0)
                    <div>
                        <h3 class="text-[11px] font-black uppercase tracking-[0.2em] text-gray-500 mb-4 ml-2">Pilotos Encontrados</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @foreach($users as $user)
                                <a href="{{ route('social.profile', $user->id) }}" class="flex items-center gap-3 p-3 bg-[#1B1D21] border border-[#2d3136] rounded-lg hover:border-[#E10600] transition-colors group">
                                    <img src="{{ $user->profile_photo_url }}" class="w-10 h-10 rounded-full object-cover">
                                    <div>
                                        <p class="font-bold text-sm text-white group-hover:text-[#E10600] transition-colors">{{ $user->name }}</p>
                                        <p class="text-[10px] text-gray-500 uppercase tracking-widest font-bold">@ {{ $user->username ?? 'piloto' }}</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Sección Hashtags --}}
                @if(count($hashtags) > 0)
                    <div>
                        <h3 class="text-[11px] font-black uppercase tracking-[0.2em] text-gray-500 mb-4 ml-2">Etiquetas Sugeridas</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($hashtags as $tag)
                                <button wire:click="$set('searchQuery', '#{{ $tag->name }}')" class="px-4 py-2 bg-[#23262A] border border-[#2d3136] rounded-lg hover:border-[#3FA9F5] transition-all flex items-center gap-2">
                                    <span class="text-[#3FA9F5] font-bold">#{{ $tag->name }}</span>
                                    <span class="text-[9px] text-gray-500 font-bold uppercase">{{ $tag->posts_count }} POSTS</span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Sección Posts --}}
                @if(count($posts) > 0)
                    <div>
                        <h3 class="text-[11px] font-black uppercase tracking-[0.2em] text-gray-500 mb-4 ml-2">Publicaciones relacionadas</h3>
                        <div class="space-y-4">
                            @foreach($posts as $post)
                                @livewire('post-item', ['post' => $post], key('search-post-'.$post->id))
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Sin resultados --}}
                @if(count($users) == 0 && count($posts) == 0 && count($hashtags) == 0)
                    <div class="text-center py-20">
                        <p class="text-gray-500">No hemos encontrado nada para "{{ $searchQuery }}"</p>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>
