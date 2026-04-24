<div class="bg-[#23262A] border border-[#2d3136] rounded p-4 mb-6 shadow-lg">
    <form wire:submit.prevent="save">
        
        <!-- Textarea para el contenido -->
        <textarea 
            wire:model="content" 
            class="w-full bg-[#1B1D21] border border-[#2d3136] rounded text-white focus:ring-[#E10600] focus:border-[#E10600] placeholder-gray-500 text-sm" 
            rows="3" 
            placeholder="¿Qué tienes en mente para esta sesión?..."
        ></textarea>
        @error('content') <span class="text-[#E10600] text-xs font-bold">{{ $message }}</span> @enderror

        <!-- Previsualización de la imagen -->
        @if ($media)
            <div class="mt-4 relative">
                <img src="{{ $media->temporaryUrl() }}" class="rounded border border-[#2d3136] max-h-48 object-cover">
            </div>
        @endif

        <div class="mt-4 flex items-center justify-between">
            <!-- Botón de subir archivo (Camuflado como icono/texto) -->
            <div>
                <input type="file" id="mediaUpload" wire:model="media" class="hidden" accept="image/*">
                <label for="mediaUpload" class="cursor-pointer text-[10px] font-bold uppercase tracking-widest text-gray-400 hover:text-white transition-colors flex items-center gap-2">
                    <span class="text-[#E10600] text-lg">+</span> FOTO / MEDIA
                </label>
                @error('media') <span class="text-[#E10600] text-xs font-bold block mt-1">{{ $message }}</span> @enderror
            </div>

            <!-- Botón de publicar -->
            <button type="submit" class="bg-[#E10600] hover:bg-[#ff0700] text-white text-[10px] font-black uppercase tracking-widest px-6 py-2 rounded italic transition-all active:scale-95 disabled:opacity-50" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">PUBLICAR</span>
                <span wire:loading wire:target="save">ENVIANDO...</span>
            </button>
        </div>
    </form>
</div>
