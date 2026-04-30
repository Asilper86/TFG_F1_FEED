<div class="bg-[#1B1D21] border border-[#2d3136] rounded-xl p-4 mb-4">
    <div class="flex gap-3">
        <!-- Avatar Izquierda -->
        <div class="flex-shrink-0">
            <img src="{{ auth()->user()->profile_photo_url }}" class="w-10 h-10 sm:w-12 sm:h-12 rounded-full object-cover">
        </div>

        <!-- Área de Posteo Derecha -->
        <div class="flex-1">
            <form wire:submit.prevent="save">
                <textarea 
                    wire:model="content" 
                    class="w-full bg-transparent border-none text-white focus:ring-0 placeholder-gray-500 text-[16px] sm:text-[18px] p-0 resize-none" 
                    rows="2" 
                    placeholder="¿Qué está pasando en pista?"
                ></textarea>

                @if ($media)
                    <div class="mt-2 relative">
                        <img src="{{ $media->temporaryUrl() }}" class="rounded-2xl border border-[#2d3136] max-h-80 object-cover w-full">
                        <button type="button" wire:click="$set('media', null)" class="absolute top-2 right-2 bg-black/60 text-white rounded-full p-1.5 hover:bg-black">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </button>
                    </div>
                @endif

                <div class="mt-3 pt-3 border-t border-[#2d3136]/50 flex items-center justify-between">
                    <div class="flex items-center gap-1">
                        <input type="file" id="mediaUpload" wire:model="media" class="hidden" accept="image/*">
                        <label for="mediaUpload" class="cursor-pointer p-2 rounded-full hover:bg-[#E10600]/10 text-[#E10600] transition-colors" title="Añadir imagen">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </label>
                    </div>

                    <button type="submit" class="bg-[#E10600] hover:bg-[#ff0700] text-white text-[14px] font-bold px-5 py-1.5 rounded-full transition-all active:scale-95 disabled:opacity-50" wire:loading.attr="disabled">
                        Postear
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
