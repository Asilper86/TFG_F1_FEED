<div class="bg-[#1B1D21] border border-white/5 rounded-xl p-4 mb-6">
    <form wire:submit.prevent="save">
        <div class="flex gap-4">
            <img src="{{ auth()->user()->profile_photo_url }}" class="w-10 h-10 rounded-full border border-white/10 hidden sm:block">
            <div class="flex-1 min-w-0">
                <textarea 
                    wire:model="content" 
                    class="w-full bg-transparent border-none text-white focus:ring-0 placeholder-gray-600 text-sm p-0 resize-none" 
                    rows="2" 
                    placeholder="¿Qué tienes en mente para esta sesión?..."
                ></textarea>
                
                @if ($media)
                    <div class="mt-3 relative inline-block">
                        <img src="{{ $media->temporaryUrl() }}" class="rounded-lg border border-white/5 max-h-40 object-cover">
                        <button type="button" wire:click="$set('media', null)" class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full p-1 shadow-lg">
                            <i class="fa-solid fa-xmark text-[10px]"></i>
                        </button>
                    </div>
                @endif

                <div class="mt-3 pt-3 border-t border-white/5 flex items-center justify-between">
                    <label class="cursor-pointer group flex items-center gap-2">
                        <input type="file" wire:model="media" class="hidden" accept="image/*">
                        <i class="fa-regular fa-image text-gray-500 group-hover:text-[#E10600] transition-colors"></i>
                        <span class="text-[10px] font-bold text-gray-500 group-hover:text-white uppercase tracking-widest transition-colors">Media</span>
                    </label>

                    <button type="submit" class="bg-[#E10600] hover:bg-red-600 text-white text-[11px] font-black uppercase tracking-widest px-6 py-1.5 rounded-lg transition-all active:scale-95 disabled:opacity-50" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">Publicar</span>
                        <span wire:loading wire:target="save">...</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
