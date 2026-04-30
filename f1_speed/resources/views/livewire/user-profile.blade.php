<div class="py-6 sm:py-12 bg-[#121418] min-h-screen text-white font-sans">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header del Perfil -->
        <div class="bg-[#1B1D21] border border-[#2d3136] rounded-lg overflow-hidden mb-8 shadow-2xl">
            <div class="h-32 sm:h-48 bg-gradient-to-r from-[#E10600] to-black"></div>
            <div class="px-6 sm:px-10 pb-10 relative">
                <div class="flex justify-between items-end -mt-12 sm:-mt-16 mb-6">
                    <img src="{{ $profileUser->profile_photo_url }}"
                        class="w-24 h-24 sm:w-32 sm:h-32 rounded-full border-4 border-[#1B1D21] object-cover bg-[#1B1D21]">
                    
                    @if (auth()->id() === $profileUser->id)
                        <button wire:click="openEditModal" class="px-6 py-2 rounded font-black text-xs uppercase tracking-widest border border-[#2d3136] hover:bg-white hover:text-black transition-all">
                            Editar Perfil
                        </button>
                    @elseif(auth()->check())
                        <button wire:click="toggleFollow" class="px-8 py-2.5 rounded font-black text-xs uppercase tracking-widest transition-all {{ $isFollowing ? 'bg-[#2d3136] text-white' : 'bg-[#E10600] text-white hover:bg-[#ff0700]' }}">
                            {{ $isFollowing ? 'Siguiendo' : 'Seguir Piloto' }}
                        </button>
                    @endif
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-3xl sm:text-4xl font-black uppercase italic tracking-tighter">{{ $profileUser->name }}</h1>
                        <p class="text-gray-500 text-sm font-bold uppercase tracking-widest mt-1">@pilot_{{ $profileUser->id }}</p>
                    </div>
                    
                    <div class="flex gap-8 border-l border-[#2d3136] pl-8">
                        <button wire:click="openFollowersModal" class="text-center group">
                            <span class="block text-2xl font-black group-hover:text-[#E10600] transition-colors">{{ $followersCount }}</span>
                            <span class="text-[10px] text-gray-500 uppercase tracking-[0.2em] font-bold">Seguidores</span>
                        </button>
                        <button wire:click="openFollowingModal" class="text-center group">
                            <span class="block text-2xl font-black group-hover:text-[#E10600] transition-colors">{{ $followingCount }}</span>
                            <span class="text-[10px] text-gray-500 uppercase tracking-[0.2em] font-bold">Siguiendo</span>
                        </button>
                    </div>
                </div>

                @if ($profileUser->bio)
                    <p class="mt-6 text-gray-300 text-sm leading-relaxed max-w-2xl italic">"{{ $profileUser->bio }}"</p>
                @endif
            </div>
        </div>

        <!-- Muro del Piloto -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <div class="lg:col-span-3 space-y-6">
                <div class="flex items-center gap-3 pb-2 border-b border-[#2d3136] mb-4">
                    <span class="w-2 h-6 bg-[#E10600]"></span>
                    <h2 class="text-lg font-black uppercase italic tracking-widest">Actividad del Piloto</h2>
                </div>

                @forelse ($posts as $post)
                    @livewire('post-item', ['post' => $post], key('post-' . $post->id))
                @empty
                    <div class="bg-[#1B1D21] border border-[#2d3136] rounded-lg p-12 text-center">
                        <p class="text-gray-500 text-xs uppercase tracking-[0.3em] font-bold">Sin telemetría</p>
                    </div>
                @endforelse
            </div>

            <div class="lg:col-span-1">
                <div class="bg-[#1B1D21] border border-[#2d3136] rounded-lg p-6 sticky top-6">
                    <h3 class="text-[10px] font-black uppercase tracking-widest text-[#E10600] mb-4">Ficha Técnica</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500 uppercase font-bold">Miembro desde</span>
                            <span class="text-white font-mono">{{ $profileUser->created_at->format('M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    </div>
    @if ($showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 backdrop-blur-sm p-4">
            <div class="bg-[#1B1D21] border border-[#2d3136] rounded-lg shadow-2xl w-full max-w-md overflow-hidden">
                <div class="p-6 border-b border-[#2d3136] flex justify-between items-center">
                    <h3 class="text-lg font-black uppercase tracking-widest text-[#E10600]">Editar Piloto</h3>
                    <button wire:click="$set('showEditModal', false)"
                        class="text-gray-500 hover:text-white">&times;</button>
                </div>

                <form wire:submit.prevent="saveProfile" class="p-6 space-y-4">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-1">Nombre
                            de Usuario (@)</label>
                        <input type="text" wire:model="username"
                            class="w-full bg-[#121418] border border-[#2d3136] rounded p-2 text-sm text-white focus:border-[#E10600] focus:ring-0 transition-colors"
                            placeholder="tu_usuario">
                        @error('username')
                            <span class="text-[#E10600] text-[10px] font-bold">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label
                            class="block text-[10px] font-black uppercase tracking-widest text-gray-500 mb-1">Biografía</label>
                        <textarea wire:model="bio" rows="3"
                            class="w-full bg-[#121418] border border-[#2d3136] rounded p-2 text-sm text-white focus:border-[#E10600] focus:ring-0 transition-colors"
                            placeholder="Cuéntanos sobre tu carrera..."></textarea>
                        @error('bio')
                            <span class="text-[#E10600] text-[10px] font-bold">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" wire:click="$set('showEditModal', false)"
                            class="flex-1 px-4 py-2 bg-transparent border border-[#2d3136] text-white text-[10px] font-black uppercase tracking-widest hover:bg-gray-800 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2 bg-[#E10600] text-white text-[10px] font-black uppercase tracking-widest hover:bg-[#ff0700] transition-colors">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($showFollowersModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 backdrop-blur-sm p-4">
            <div class="bg-[#1B1D21] border border-[#2d3136] rounded-lg shadow-2xl w-full max-w-md overflow-hidden">
                <div class="p-6 border-b border-[#2d3136] flex justify-between items-center">
                    <h3 class="text-lg font-black uppercase tracking-widest text-[#E10600]">Seguidores</h3>
                    <button wire:click="$set('showFollowersModal', false)"
                        class="text-gray-500 hover:text-white">&times;</button>
                </div>

                <div class="p-4 max-h-96 overflow-y-auto space-y-4">
                    @foreach ($profileUser->followers as $follower)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <img src="{{ $follower->profile_photo_url }}"
                                    class="w-10 h-10 rounded-full object-cover">
                                <span class="text-sm font-bold">{{ $follower->name }}</span>
                            </div>
                            <a href="/profile/{{ $follower->id }}"
                                class="text-[10px] font-black uppercase tracking-widest text-gray-500 hover:text-white">Ver
                                Perfil</a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
    @if ($showFollowingModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 backdrop-blur-sm p-4">
            <div class="bg-[#1B1D21] border border-[#2d3136] rounded-lg shadow-2xl w-full max-w-md overflow-hidden">
                <div class="p-6 border-b border-[#2d3136] flex justify-between items-center">
                    <h3 class="text-lg font-black uppercase tracking-widest text-white">Siguiendo</h3>
                    <button wire:click="$set('showFollowingModal', false)"
                        class="text-gray-500 hover:text-white">&times;</button>
                </div>

                <div class="p-4 max-h-96 overflow-y-auto space-y-4">
                    @foreach ($profileUser->following as $followed)
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <img src="{{ $followed->profile_photo_url }}"
                                    class="w-10 h-10 rounded-full object-cover">
                                <span class="text-sm font-bold">{{ $followed->name }}</span>
                            </div>
                            <a href="/profile/{{ $followed->id }}"
                                class="text-[10px] font-black uppercase tracking-widest text-gray-500 hover:text-white">Ver
                                Perfil</a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif


</div>
