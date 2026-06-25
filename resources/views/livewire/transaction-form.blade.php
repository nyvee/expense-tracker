<div x-data="{ open: false }" @open-tx-modal.window="open = true" @close-tx-modal.window="open = false" style="display: none;" x-show="open" class="relative z-50">
    <!-- Backdrop -->
    <div x-show="open" x-transition.opacity class="fixed inset-0 bg-gray-900/50 max-w-md mx-auto"></div>

    <!-- Bottom Sheet -->
    <div 
        x-show="open" 
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-y-full"
        x-transition:enter-end="translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-y-0"
        x-transition:leave-end="translate-y-full"
        class="fixed bottom-0 left-0 right-0 w-full max-w-md mx-auto bg-[#111111] rounded-t-3xl shadow-[0_-10px_40px_rgba(0,0,0,0.5)] z-50 pb-8 pt-4 px-6 border-t border-white/10 h-[80vh] overflow-y-auto">
        
        <div class="w-12 h-1.5 bg-white/20 rounded-full mx-auto mb-6"></div>
        
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-white">Add Transaction</h3>
            <button @click="open = false" class="text-gray-400 bg-transparent hover:bg-white/10 hover:text-white rounded-full text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form wire:submit="save" class="space-y-4">
            
            <div class="grid grid-cols-3 gap-2 mb-2">
                <label class="cursor-pointer">
                    <input type="radio" wire:model.live="arus" value="Out" class="peer sr-only">
                    <div class="p-2 text-center bg-[#1c1c1c] border border-white/10 rounded-xl peer-checked:bg-rose-500/20 peer-checked:border-rose-500 peer-checked:text-rose-400 transition-all font-bold text-[11px] text-gray-400">Expense</div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" wire:model.live="arus" value="In" class="peer sr-only">
                    <div class="p-2 text-center bg-[#1c1c1c] border border-white/10 rounded-xl peer-checked:bg-[#cbfc1b]/20 peer-checked:border-[#cbfc1b] peer-checked:text-[#cbfc1b] transition-all font-bold text-[11px] text-gray-400">Income</div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" wire:model.live="arus" value="Transfer" class="peer sr-only">
                    <div class="p-2 text-center bg-[#1c1c1c] border border-white/10 rounded-xl peer-checked:bg-blue-500/20 peer-checked:border-blue-500 peer-checked:text-blue-400 transition-all font-bold text-[11px] text-gray-400">Transfer</div>
                </label>
            </div>

            <div>
                <label class="block mb-1.5 text-xs font-bold text-white">Amount (Rp)</label>
                <input type="number" wire:model="nominal" required placeholder="0" class="bg-[#1c1c1c] border border-white/10 text-white text-lg font-bold rounded-xl focus:ring-[#cbfc1b] focus:border-[#cbfc1b] block w-full p-3 shadow-sm placeholder-gray-500">
            </div>

            <div>
                <label class="block mb-1.5 text-xs font-bold text-white">Name / Merchant</label>
                <input type="text" wire:model="merchant" required placeholder="e.g., Lunch" class="bg-[#1c1c1c] border border-white/10 text-white text-sm font-medium rounded-xl focus:ring-[#cbfc1b] focus:border-[#cbfc1b] block w-full p-3 shadow-sm placeholder-gray-500">
            </div>

            <div class="grid grid-cols-2 gap-3">
                @if($arus !== 'Transfer')
                <div>
                    <label class="block mb-1.5 text-xs font-bold text-white">Category</label>
                    <select wire:model="kategori" required class="bg-[#1c1c1c] border border-white/10 text-white text-sm rounded-xl focus:ring-[#cbfc1b] focus:border-[#cbfc1b] block w-full p-3 font-medium shadow-sm">
                        <option value="">Select...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                <div>
                    <label class="block mb-1.5 text-xs font-bold text-white">{{ $arus === 'Transfer' ? 'From Account' : 'Account Source' }}</label>
                    <select wire:model="sumber" required class="bg-[#1c1c1c] border border-white/10 text-white text-sm rounded-xl focus:ring-[#cbfc1b] focus:border-[#cbfc1b] block w-full p-3 font-medium shadow-sm">
                        <option value="Cash">Cash</option>
                        <option value="BCA">BCA</option>
                        <option value="BLU">BLU</option>
                        <option value="Line Bank">Line Bank</option>
                    </select>
                </div>
                
                @if($arus === 'Transfer')
                <div>
                    <label class="block mb-1.5 text-xs font-bold text-white">To Account</label>
                    <select wire:model="transfer_ke" required class="bg-[#1c1c1c] border border-white/10 text-white text-sm rounded-xl focus:ring-[#cbfc1b] focus:border-[#cbfc1b] block w-full p-3 font-medium shadow-sm">
                        <option value="Cash">Cash</option>
                        <option value="BCA">BCA</option>
                        <option value="BLU">BLU</option>
                        <option value="Line Bank">Line Bank</option>
                    </select>
                </div>
                @endif
            </div>

            <div>
                <label class="block mb-1.5 text-xs font-bold text-white">Date</label>
                <input type="datetime-local" wire:model="tanggal" required class="bg-[#1c1c1c] border border-white/10 text-white text-sm font-medium rounded-xl focus:ring-[#cbfc1b] focus:border-[#cbfc1b] block w-full p-3 shadow-sm">
            </div>

            <button type="submit" class="w-full text-black bg-[#cbfc1b] hover:bg-[#b5e016] focus:ring-4 focus:outline-none focus:ring-[#cbfc1b]/50 font-bold rounded-xl text-sm px-5 py-3.5 text-center shadow-[0_8px_20px_rgb(203,252,27,0.3)] mt-2 transition-colors">
                Save Transaction
            </button>
        </form>
    </div>
</div>
