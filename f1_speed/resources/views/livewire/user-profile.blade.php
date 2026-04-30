<div class="py-6 sm:py-10 bg-[#0B0C0E] min-h-screen text-white font-sans">
    <div class="max-w-5xl mx-auto px-4 sm:px-6">
        <!-- Profile Header Card -->
        <div class="bg-[#1B1D21] border border-white/5 rounded-2xl overflow-hidden mb-8 shadow-2xl">
            <div class="h-32 sm:h-44 bg-gradient-to-r from-[#E10600] to-black relative">
                <div class="absolute inset-0 bg-black/20"></div>
            </div>
            <div class="px-6 sm:px-8 pb-8 relative">
                <div class="flex justify-between items-end -mt-12 sm:-mt-16 mb-6">
                    <div class="relative group">
                        <img src="{{ $profileUser->profile_photo_url }}"
                            class="w-24 h-24 sm:w-32 sm:h-32 rounded-full border-4 border-[#1B1D21] object-cover bg-[#1B1D21] shadow-2xl">
                    </div>
                    
                    <div class="flex items-center gap-3">
                        @if (auth()->id() === $profileUser->id)
                            <button wire:click="openEditModal" class="px-5 py-2 rounded-xl bg-white/5 border border-white/10 text-[11px] font-black uppercase tracking-widest hover:bg-white/10 transition-all">
                                Editar Perfil
                            </button>
                        @elseif(auth()->check())
                            <button wire:click="toggleFollow" class="px-6 py-2 rounded-xl font-black text-[11px] uppercase tracking-widest transition-all {{ $isFollowing ? 'bg-white/5 border border-white/10 text-white' : 'bg-[#E10600] text-white hover:bg-red-600 shadow-[0_0_15px_rgba(225,6,0,0.3)]' }}">
                                {{ $isFollowing ? 'Siguiendo' : 'Seguir Piloto' }}
                            </button>
                        @endif
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
                    <div>
                        <h1 class="text-3xl font-black uppercase italic tracking-tighter">{{ $profileUser->name }}</h1>
                        <p class="text-gray-500 text-xs font-bold uppercase tracking-[0.2em] mt-1 flex items-center gap-2">
                            @pilot_{{ $profileUser->id }} 
                            <span class="w-1 h-1 bg-gray-700 rounded-full"></span>
                            <span class="text-gray-600">Miembro desde {{ $profileUser->created_at->format('M Y') }}</span>
                        </p>
                        @if ($profileUser->bio)
                            <p class="mt-4 text-gray-400 text-sm leading-relaxed italic border-l-2 border-[#E10600]/30 pl-4">"{{ $profileUser->bio }}"</p>
                        @endif
                    </div>
                    
                    <div class="flex gap-10 md:justify-end">
                        <button wire:click="openFollowersModal" class="text-center group">
                            <span class="block text-2xl font-black group-hover:text-[#E10600] transition-colors leading-none">{{ $followersCount }}</span>
                            <span class="text-[9px] text-gray-500 uppercase tracking-[0.3em] font-black mt-1 block">Seguidores</span>
                        </button>
                        <button wire:click="openFollowingModal" class="text-center group">
                            <span class="block text-2xl font-black group-hover:text-[#E10600] transition-colors leading-none">{{ $followingCount }}</span>
                            <span class="text-[9px] text-gray-500 uppercase tracking-[0.3em] font-black mt-1 block">Siguiendo</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activity Grid -->
        <div class="flex items-center gap-3 mb-6">
            <div class="w-1.5 h-5 bg-[#E10600]"></div>
            <h2 class="text-sm font-black uppercase tracking-[0.3em] text-white italic">Muro de Actividad</h2>
        </div>

        <div class="space-y-4">
            @forelse ($posts as $post)
                @livewire('post-item', ['post' => $post], key('post-' . $post->id))
            @empty
                <div class="bg-[#1B1D21] border border-white/5 rounded-2xl p-16 text-center">
                    <i class="fa-solid fa-ghost text-3xl text-gray-800 mb-4"></i>
                    <p class="text-gray-600 text-[10px] uppercase tracking-[0.4em] font-black italic">Sin actividad registrada</p>
                </div>
            @endforelse
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
