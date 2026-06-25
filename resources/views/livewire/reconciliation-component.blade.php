<div class="p-4 pb-24 font-sans" x-data="{ isModalOpen: false }" @close-modal.window="isModalOpen = false">
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-white">Review Queue</h2>
            <p class="text-xs font-medium text-gray-400 mt-1">Review uncategorized transactions.</p>
        </div>
        <div class="flex items-center gap-3">
            @if($transactions->isNotEmpty())
            <button wire:click="autoMatchTransfers" wire:loading.attr="disabled" class="bg-[#cbfc1b]/10 text-[#cbfc1b] border border-[#cbfc1b]/30 hover:bg-[#cbfc1b]/20 px-3 py-1.5 rounded-lg text-xs font-bold transition-colors flex items-center gap-1.5">
                <i data-lucide="wand-2" class="w-3.5 h-3.5"></i>
                <span class="hidden sm:inline">Auto-Match</span>
            </button>
            @endif
            <div class="bg-red-500/20 text-red-400 text-sm font-bold p-3 rounded-full border border-red-500/30">
                {{ $transactions->count() }}
            </div>
        </div>
    </div>

    @if($transactions->isEmpty())
        <div class="flex flex-col items-center justify-center p-8 bg-[#1c1c1c] rounded-[1.5rem] shadow-sm border border-white/5 mt-10">
            <div class="w-16 h-16 bg-[#cbfc1b]/10 text-[#cbfc1b] rounded-full flex items-center justify-center mb-3">
                <i data-lucide="check-circle" class="w-8 h-8"></i>
            </div>
            <h3 class="text-base font-bold text-white">All Clear!</h3>
            <p class="text-xs text-gray-400 text-center mt-1 font-medium">No transactions need to be reviewed.</p>
            <a href="/" class="mt-6 text-black bg-[#cbfc1b] hover:bg-[#b5e016] focus:ring-4 focus:ring-[#cbfc1b]/50 font-bold rounded-lg text-sm px-5 py-2.5 transition-colors">Back to Dashboard</a>
        </div>
    @else
        <div class="space-y-3">
            @foreach($transactions as $tx)
                <div class="bg-[#1c1c1c] rounded-[1rem] shadow-sm border border-white/5 p-4">
                    <div class="flex justify-between items-start mb-3">
                        <div class="pr-2">
                            <p class="text-[13px] font-bold text-white break-all leading-tight">{{ $tx->merchant }}</p>
                            <p class="text-[10px] font-medium text-gray-400 mt-1">{{ \Carbon\Carbon::parse($tx->tanggal)->format('d M Y, H:i') }} &middot; {{ $tx->sumber }}</p>
                        </div>
                        <div class="text-[13px] font-bold {{ $tx->arus === 'In' ? 'text-emerald-500' : 'text-rose-500' }} shrink-0">
                            {{ $tx->arus === 'In' ? '+' : '-' }}Rp {{ number_format($tx->nominal, 0, ',', '.') }}
                        </div>
                    </div>

                    @if($this->isOjol($tx->merchant))
                        <!-- Ojol Quick Actions -->
                        <div class="inline-flex rounded-lg shadow-sm w-full" role="group">
                            <button wire:click="quickAction('{{ $tx->id }}', 'Transportasi')" type="button" class="flex-1 px-2 py-2 text-xs font-bold text-white bg-[#2a2a2a] border border-white/10 rounded-s-lg hover:bg-white/10 focus:z-10 transition-colors">
                                Ride
                            </button>
                            <button wire:click="quickAction('{{ $tx->id }}', 'Makan & Minum')" type="button" class="flex-1 px-2 py-2 text-xs font-bold text-white bg-[#2a2a2a] border-t border-b border-white/10 hover:bg-white/10 focus:z-10 transition-colors">
                                Food
                            </button>
                            <button wire:click="quickAction('{{ $tx->id }}', 'Logistik')" type="button" class="flex-1 px-2 py-2 text-xs font-bold text-white bg-[#2a2a2a] border border-white/10 rounded-e-lg hover:bg-white/10 focus:z-10 transition-colors">
                                Send
                            </button>
                        </div>
                    @else
                        <!-- General Action -->
                        <button 
                            @click="isModalOpen = true" 
                            wire:click="openModal('{{ $tx->id }}', '{{ addslashes($tx->merchant) }}')"
                            class="w-full text-black bg-[#cbfc1b] border border-[#cbfc1b] hover:bg-[#b5e016] focus:ring-4 focus:ring-[#cbfc1b]/50 font-bold rounded-lg text-xs px-4 py-2.5 text-center shadow-sm transition-colors">
                            Select Category
                        </button>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <!-- Bottom Sheet Modal (Alpine.js) -->
    <div x-show="isModalOpen" style="display: none;" class="relative z-50">
        <!-- Backdrop -->
        <div x-show="isModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900/50 max-w-md mx-auto"></div>

        <!-- Sheet -->
        <div 
            x-show="isModalOpen" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
            class="fixed bottom-0 left-0 right-0 w-full max-w-md mx-auto bg-[#111111] rounded-t-3xl shadow-[0_-10px_40px_rgba(0,0,0,0.5)] z-50 pb-8 pt-4 px-6 border-t border-white/10">
            
            <div class="w-12 h-1.5 bg-white/20 rounded-full mx-auto mb-6"></div>
            
            <h3 class="text-xl font-bold text-white mb-1">Set Category</h3>
            <p class="text-xs font-medium text-gray-400 mb-6 truncate">{{ $activeMerchant }}</p>

            <form wire:submit.prevent="saveRuleAndReview" class="space-y-5">
                <div>
                    <label class="block mb-2 text-xs font-bold text-white">Select New Category</label>
                    <select wire:model.live="selectedCategory" class="bg-[#1c1c1c] border border-white/10 text-white text-sm rounded-xl focus:ring-[#cbfc1b] focus:border-[#cbfc1b] block w-full p-3 font-medium shadow-sm">
                        <option value="">-- Please Select --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}">{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Toggle Switch -->
                <div class="flex items-center justify-between p-3 bg-[#1c1c1c] rounded-xl border border-white/5">
                    <span class="text-xs font-bold text-gray-300">Remember this rule for the future</span>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" wire:model.live="rememberRule" class="sr-only peer">
                        <div class="relative w-11 h-6 bg-white/20 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#cbfc1b]"></div>
                    </label>
                </div>

                <!-- Keyword input shown if remember is true -->
                @if($rememberRule)
                <div class="p-3 bg-[#cbfc1b]/10 rounded-xl border border-[#cbfc1b]/30 transition-all">
                    <label class="block mb-1.5 text-[10px] font-bold text-[#cbfc1b] uppercase tracking-wide">Matching Keyword:</label>
                    <input type="text" wire:model="newKeyword" class="bg-[#1c1c1c] border border-white/10 text-white text-xs font-medium rounded-lg focus:ring-[#cbfc1b] focus:border-[#cbfc1b] block w-full p-2.5 shadow-sm">
                    <p class="text-[9px] font-medium text-[#cbfc1b]/70 mt-1.5">Modify the keyword to be more general (e.g., remove unique random codes).</p>
                </div>
                @endif

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="isModalOpen = false" class="flex-1 py-3 px-4 text-sm font-bold text-white bg-transparent rounded-xl border border-white/20 hover:bg-white/10 transition-colors">Cancel</button>
                    <button type="submit" class="flex-1 text-black bg-[#cbfc1b] hover:bg-[#b5e016] focus:ring-4 focus:outline-none focus:ring-[#cbfc1b]/50 font-bold rounded-xl text-sm px-4 py-3 text-center shadow-md transition-colors">Save & Review</button>
                </div>
            </form>
        </div>
    </div>
</div>
