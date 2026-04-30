<div class="bg-[#1B1D21] border border-[#2d3136] rounded-lg p-4 shadow-md mb-6">
    <form wire:submit.prevent="save">
        <div class="flex gap-4">
            <!-- Avatar -->
            <div class="shrink-0">
                <img src="{{ auth()->user()->profile_photo_url }}" class="w-10 h-10 rounded-full border border-[#2d3136] object-cover hidden sm:block">
            </div>

            <!-- Input area -->
            <div class="flex-1 min-w-0">
                <textarea 
                    wire:model="content" 
                    class="w-full bg-[#121418] border border-[#2d3136] rounded-md text-white focus:ring-[#E10600] focus:border-[#E10600] placeholder-gray-500 text-sm p-3 resize-none" 
                    rows="2" 
                    placeholder="¿Qué tienes en mente?..."
                ></textarea>
                
                @if ($media)
                    <div class="mt-3 relative inline-block">
                        <img src="{{ $media->temporaryUrl() }}" class="rounded border border-[#2d3136] max-h-40 object-cover">
                        <button type="button" wire:click="$set('media', null)" class="absolute -top-2 -right-2 bg-[#E10600] text-white rounded-full w-5 h-5 flex items-center justify-center shadow-lg">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                @endif

                <div class="mt-3 flex items-center justify-between">
                    <label class="cursor-pointer text-gray-400 hover:text-[#E10600] transition-colors flex items-center gap-2">
                        <input type="file" wire:model="media" class="hidden" accept="image/*">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span class="text-[10px] font-bold uppercase tracking-widest hidden sm:inline">Añadir Media</span>
                    </label>

                    <button type="submit" class="bg-[#E10600] hover:bg-red-700 text-white text-[11px] font-black uppercase tracking-widest px-6 py-2 rounded transition-all disabled:opacity-50" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">Publicar</span>
                        <span wire:loading wire:target="save">...</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
