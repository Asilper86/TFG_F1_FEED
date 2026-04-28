<div class="py-12 bg-[#121418] min-h-screen text-white">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

        <!-- Header del Perfil -->
        <div class="bg-[#1B1D21] border border-[#2d3136] rounded-lg overflow-hidden mb-8 shadow-lg">
            <!-- Banner superior -->
            <div class="h-32 bg-gradient-to-r from-[#E10600] to-black"></div>

            <div class="px-8 pb-8 relative">
                <div class="flex justify-between items-end -mt-12 mb-4">
                    <img src="{{ $profileUser->profile_photo_url }}"
                        class="w-24 h-24 rounded-full border-4 border-[#1B1D21] object-cover bg-[#1B1D21]">

                    @if (auth()->id() !== $profileUser->id)
                        <button wire:click="toggleFollow"
                            class="px-6 py-2 rounded font-bold text-xs uppercase tracking-widest transition-colors {{ $isFollowing ? 'border border-[#2d3136] bg-transparent text-white hover:border-gray-500' : 'bg-[#E10600] text-white hover:bg-[#ff0700]' }}">
                            {{ $isFollowing ? 'Siguiendo' : 'Seguir' }}
                        </button>
                    @endif
                </div>

                <h1 class="text-3xl font-black uppercase tracking-wide">{{ $profileUser->name }}</h1>
                <p class="text-gray-400 text-sm mb-6">{{ $profileUser->email }}</p>
                @if (auth()->id() === $profileUser->id)
                    <button wire:click="openEditModal"
                        class="px-6 py-2 rounded font-bold text-xs uppercase tracking-widest border border-[#2d3136] text-white hover:bg-white hover:text-black transition-all">
                        Editar Perfil
                    </button>
                @endif
                @if ($profileUser->bio)
                    <p class="text-gray-300 text-sm mt-4 italic">"{{ $profileUser->bio }}"</p>
                @endif
                <div class="flex gap-6 border-t border-[#2d3136] pt-4 mt-4">
                    <div wire:click="openFollowersModal" class="cursor-pointer hover:opacity-80 transition-opacity">
                        <span class="block text-2xl font-black text-white">{{ $followersCount }}</span>
                        <span class="text-[10px] text-gray-500 uppercase tracking-widest font-bold">Seguidores</span>
                    </div>

                    <div wire:click="openFollowingModal" class="cursor-pointer hover:opacity-80 transition-opacity">
                        <span class="block text-2xl font-black text-white">{{ $followingCount }}</span>
                        <span class="text-[10px] text-gray-500 uppercase tracking-widest font-bold">Seguidos</span>
                    </div>

                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="col-span-1 lg:col-span-2 space-y-6">
                <h2 class="text-lg font-bold text-white uppercase tracking-widest mb-4 border-b border-[#2d3136] pb-2">
                    Posts de {{ $profileUser->name }}</h2>

                @forelse ($posts as $post)
                    @livewire('post-item', ['post' => $post], key('post-' . $post->id))
                @empty
                    <div class="bg-[#1B1D21] border border-[#2d3136] rounded-md p-8 text-center">
                        <p class="text-gray-500 text-sm uppercase tracking-widest font-bold mb-2">Sin actividad</p>
                        <p class="text-gray-400 text-xs">Este piloto aún no ha publicado nada en su muro.</p>
                    </div>
                @endforelse
            </div>

            <div class="col-span-1">
                <div class="bg-[#1B1D21] border border-[#2d3136] rounded-md p-5 sticky top-6">
                    <h3 class="text-[11px] font-black uppercase tracking-widest text-gray-400 mb-4">Información del
                        Piloto</h3>
                    <ul class="space-y-3 text-sm text-gray-300">
                        <li class="flex items-center justify-between">
                            <span class="text-gray-500">Miembro desde</span>
                            <span class="font-bold">{{ $profileUser->created_at->format('M Y') }}</span>
                        </li>
                    </ul>
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
