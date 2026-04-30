<div class="bg-[#1B1D21] border border-[#2d3136] rounded-lg p-4 sm:p-6 mb-8 shadow-xl">
    <form wire:submit.prevent="save">
        <div class="flex items-start gap-4">
            <img src="{{ auth()->user()->profile_photo_url }}" class="w-10 h-10 rounded-full border border-[#2d3136] hidden sm:block">
            <div class="flex-1">
                <textarea 
                    wire:model="content" 
                    class="w-full bg-[#121418] border border-[#2d3136] rounded-lg text-white focus:ring-[#E10600] focus:border-[#E10600] placeholder-gray-600 text-sm p-4 resize-none" 
                    rows="3" 
                    placeholder="¿Qué tienes en mente para esta sesión?..."
                ></textarea>
                
                @if ($media)
                    <div class="mt-4 relative inline-block">
                        <img src="{{ $media->temporaryUrl() }}" class="rounded-lg border border-[#2d3136] max-h-48 object-cover">
                        <button type="button" wire:click="$set('media', null)" class="absolute -top-2 -right-2 bg-[#E10600] text-white rounded-full p-1 shadow-lg">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                @endif

                <div class="mt-4 flex items-center justify-between border-t border-[#2d3136] pt-4">
                    <label class="cursor-pointer text-gray-400 hover:text-white transition-colors flex items-center gap-2">
                        <input type="file" wire:model="media" class="hidden" accept="image/*">
                        <svg class="w-5 h-5 text-[#E10600]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span class="text-[10px] font-black uppercase tracking-widest">Multimedia</span>
                    </label>

                    <button type="submit" class="bg-[#E10600] hover:bg-[#ff0700] text-white text-[11px] font-black uppercase tracking-widest px-8 py-2 rounded transition-all active:scale-95 disabled:opacity-50" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="save">Publicar Post</span>
                        <span wire:loading wire:target="save">Enviando...</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
