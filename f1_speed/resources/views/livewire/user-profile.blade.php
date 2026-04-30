<div class="py-8 bg-[#121418] min-h-screen text-white font-sans">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        
        <!-- Profile Header -->
        <div class="bg-[#1B1D21] border border-[#2d3136] rounded-lg overflow-hidden mb-6 shadow-md">
            <!-- Banner -->
            <div class="h-32 sm:h-40 bg-gradient-to-r from-[#E10600] to-black"></div>
            
            <!-- Info -->
            <div class="px-4 sm:px-6 pb-6 relative">
                <!-- Avatar & Actions Row -->
                <div class="flex justify-between items-end -mt-12 sm:-mt-16 mb-4">
                    <img src="{{ $profileUser->profile_photo_url }}"
                        class="w-24 h-24 sm:w-32 sm:h-32 rounded-full border-4 border-[#1B1D21] object-cover bg-[#1B1D21]">
                    
                    <div class="flex gap-2 mb-2">
                        @if (auth()->id() === $profileUser->id)
                            <button wire:click="openEditModal" class="px-4 py-1.5 rounded border border-[#2d3136] text-xs font-bold uppercase tracking-widest hover:bg-[#2d3136] transition-colors">
                                Editar
                            </button>
                        @elseif(auth()->check())
                            <button wire:click="toggleFollow" class="px-4 py-1.5 rounded text-xs font-bold uppercase tracking-widest transition-colors {{ $isFollowing ? 'border border-[#2d3136] bg-transparent text-white hover:border-gray-500' : 'bg-[#E10600] text-white hover:bg-red-700' }}">
                                {{ $isFollowing ? 'Siguiendo' : 'Seguir' }}
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Name & Bio -->
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black italic uppercase">{{ $profileUser->name }}</h1>
                    <p class="text-gray-500 text-sm">@pilot_{{ $profileUser->id }}</p>
                </div>

                @if ($profileUser->bio)
                    <p class="mt-3 text-gray-300 text-sm leading-relaxed">{{ $profileUser->bio }}</p>
                @endif

                <div class="flex gap-4 mt-4 text-sm">
                    <button wire:click="openFollowingModal" class="hover:underline">
                        <span class="font-bold text-white">{{ $followingCount }}</span> <span class="text-gray-500">Siguiendo</span>
                    </button>
                    <button wire:click="openFollowersModal" class="hover:underline">
                        <span class="font-bold text-white">{{ $followersCount }}</span> <span class="text-gray-500">Seguidores</span>
                    </button>
                    <span class="text-gray-500 ml-auto hidden sm:inline text-xs mt-1">
                        <i class="fa-regular fa-calendar mr-1"></i> Se unió en {{ $profileUser->created_at->format('M Y') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Timeline Section -->
        <div class="flex items-center gap-2 mb-4 pb-2 border-b border-[#2d3136]">
            <span class="text-white font-bold text-sm uppercase tracking-widest">Actividad</span>
        </div>

        <div class="space-y-4">
            @forelse ($posts as $post)
                @livewire('post-item', ['post' => $post], key('post-' . $post->id))
            @empty
                <div class="bg-[#1B1D21] border border-[#2d3136] rounded-lg p-10 text-center text-gray-500 text-sm">
                    Sin actividad reciente.
                </div>
            @endforelse
        </div>
    </div>
    <!-- End Main Content -->
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
