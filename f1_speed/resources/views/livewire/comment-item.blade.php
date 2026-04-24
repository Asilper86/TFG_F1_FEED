<div class="flex gap-3 group relative w-full">
    <img src="{{ $comment->user->profile_photo_url }}" class="w-8 h-8 rounded-full border border-[#2d3136] object-cover">
    
    <div class="flex-1">
        <!-- Caja del comentario principal -->
        <div class="bg-[#23262A] border border-[#2d3136] rounded p-3 relative">
            <div class="flex items-center gap-2 mb-1">
                <span class="text-[11px] font-bold text-white uppercase">{{ $comment->user->name }}</span>
                <span class="text-[9px] text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
            </div>
            <p class="text-sm text-gray-300">{{ $comment->body }}</p>
            
            @if ($comment->media_path)
                <div class="mt-2 rounded overflow-hidden border border-[#2d3136] inline-block">
                    <img src="{{ asset('storage/' . $comment->media_path) }}" class="max-h-32 object-cover">
                </div>
            @endif

            @if (auth()->id() === $comment->user_id)
                <button wire:click="deleteComment" wire:confirm="¿Borrar este comentario?" class="absolute top-3 right-3 text-gray-500 hover:text-[#E10600] opacity-0 group-hover:opacity-100 transition-all">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            @endif
        </div>

        <!-- Botones de Acción del Comentario (Likes y Respuestas) -->
        <div class="flex items-center gap-4 mt-2 ml-2">
            <button wire:click="toggleLike" class="flex items-center gap-1 transition-colors {{ $hasLiked ? 'text-[#E10600]' : 'text-gray-500 hover:text-[#E10600]' }}">
                <svg class="w-3.5 h-3.5 {{ $hasLiked ? 'fill-current' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                <span class="text-[9px] font-bold">{{ $likesCount }}</span>
            </button>
            
            <button wire:click="toggleReplies" class="flex items-center gap-1 text-gray-500 hover:text-[#3FA9F5] transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                <span class="text-[9px] font-bold">{{ $repliesCount }} RESPUESTAS</span>
            </button>
        </div>

        <!-- Zona de Respuestas anidadas (Hilos) -->
        @if ($showReplies)
            <div class="mt-3 ml-6 pl-3 border-l border-[#2d3136]">
                <!-- Formulario de respuesta al comentario -->
                                <!-- Formulario de respuesta al comentario -->
                                <form wire:submit.prevent="addReply" class="mb-4">
                                    <div class="flex gap-2 items-center">
                                        <input type="text" wire:model="newReply" placeholder="Responde a {{ $comment->user->name }}..." class="flex-1 bg-transparent border-b border-[#2d3136] focus:border-[#E10600] text-gray-300 text-xs py-1 px-0 shadow-none focus:ring-0">
                                        
                                        <!-- Botón de adjuntar foto -->
                                        <div>
                                            <input type="file" id="replyMediaUpload-{{ $comment->id }}" wire:model="replyMedia" class="hidden" accept="image/*">
                                            <label for="replyMediaUpload-{{ $comment->id }}" class="cursor-pointer text-gray-500 hover:text-[#E10600] transition-colors flex items-center justify-center">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            </label>
                                        </div>
                
                                        <button type="submit" class="text-[#E10600] font-bold text-[9px] uppercase tracking-widest hover:text-[#ff0700]" wire:loading.attr="disabled" wire:target="addReply">Enviar</button>
                                    </div>
                
                                    @error('newReply') <span class="text-[#E10600] text-[10px] block mt-1">{{ $message }}</span> @enderror
                                    @error('replyMedia') <span class="text-[#E10600] text-[10px] block mt-1">{{ $message }}</span> @enderror
                
                                    <!-- Previsualización de la foto seleccionada -->
                                    @if ($replyMedia)
                                        <div class="mt-2 relative inline-block">
                                            <img src="{{ $replyMedia->temporaryUrl() }}" class="h-16 rounded border border-[#2d3136] object-cover">
                                        </div>
                                    @endif
                                </form>
                

                <!-- Lista de respuestas a este comentario -->
                <div class="space-y-4 mt-4">
                    @foreach ($comment->comments as $reply)
                        
                        <!-- Aquí ocurre la magia: el componente se llama a sí mismo -->
                        @livewire('comment-item', ['comment' => $reply], key('reply-'.$reply->id))
                        
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
