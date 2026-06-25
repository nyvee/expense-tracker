<div class="p-4 pb-28 font-sans" x-data="{ isEditModalOpen: false }" @open-edit-modal.window="isEditModalOpen = true" @close-edit-modal.window="isEditModalOpen = false">
    <div class="mb-6">
        <h2 class="text-xl font-bold text-white">Transaction History</h2>
        <p class="text-xs font-medium text-gray-400 mt-1">Track all your financial activities.</p>
    </div>

    <!-- Filters -->
    <div class="space-y-3 mb-6">
        <!-- Date Range -->
        <div class="flex gap-3">
            <div class="w-1/2">
                <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase tracking-wider">Start Date</label>
                <input type="date" wire:model.live="startDate" class="bg-[#1c1c1c] border border-white/10 text-white text-xs font-bold rounded-xl focus:ring-[#cbfc1b] focus:border-[#cbfc1b] block w-full p-2.5 shadow-sm">
            </div>
            <div class="w-1/2">
                <label class="block text-[10px] font-bold text-gray-500 mb-1 uppercase tracking-wider">End Date</label>
                <input type="date" wire:model.live="endDate" class="bg-[#1c1c1c] border border-white/10 text-white text-xs font-bold rounded-xl focus:ring-[#cbfc1b] focus:border-[#cbfc1b] block w-full p-2.5 shadow-sm">
            </div>
        </div>

        <!-- Type & Category -->
        <div class="flex gap-3">
            <div class="w-1/2">
                <select wire:model.live="typeFilter" class="bg-[#1c1c1c] border border-white/10 text-white text-xs font-bold rounded-xl focus:ring-[#cbfc1b] focus:border-[#cbfc1b] block w-full p-2.5 shadow-sm">
                    <option value="">All Types</option>
                    <option value="In">Income (+)</option>
                    <option value="Out">Expense (-)</option>
                </select>
            </div>
            <div class="w-1/2">
                <select wire:model.live="categoryFilter" class="bg-[#1c1c1c] border border-white/10 text-white text-xs font-bold rounded-xl focus:ring-[#cbfc1b] focus:border-[#cbfc1b] block w-full p-2.5 shadow-sm">
                    <option value="">All Categories</option>
                    @foreach($availableCategories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Transaction List -->
    <div class="bg-[#1c1c1c] rounded-[1.5rem] shadow-sm border border-white/5 p-4">
        @if($transactions->isEmpty())
            <div class="py-10 text-center">
                <i data-lucide="inbox" class="w-12 h-12 mx-auto text-gray-600 mb-3"></i>
                <p class="text-sm font-bold text-gray-500">No history.</p>
            </div>
        @else
            <div class="flow-root">
                <ul role="list" class="divide-y divide-white/5">
                    @foreach($transactions as $tx)
                    <li class="py-3">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center justify-center w-9 h-9 text-xs font-extrabold text-white rounded-full 
                                    @if(Str::contains($tx->sumber, 'BCA')) bg-blue-600
                                    @elseif(Str::contains($tx->sumber, 'BLU')) bg-cyan-500
                                    @elseif(Str::contains($tx->sumber, 'Line')) bg-green-500
                                    @elseif(Str::contains($tx->sumber, 'Cash')) bg-amber-500
                                    @else bg-gray-500 @endif shadow-sm">
                                    {{ strtoupper(substr($tx->sumber, 0, 2)) }}
                                </span>
                            </div>
                            <div class="flex-1 min-w-0 ms-3 pr-2">
                                <p class="text-[13px] font-bold text-white truncate tracking-tight">
                                    {{ $tx->merchant }}
                                </p>
                                <p class="text-[10px] text-gray-400 truncate mt-0.5">
                                    {{ \Carbon\Carbon::parse($tx->tanggal)->format('d M, H:i') }} &middot; {{ $tx->sumber }}
                                </p>
                                <div class="mt-1.5 flex gap-1">
                                    <span class="bg-white/10 text-gray-300 text-[9px] font-bold px-2 py-0.5 rounded">{{ $tx->kategori ?? 'Uncategorized' }}</span>
                                    @if($tx->status === 'Needs Review')
                                        <span class="bg-red-500/20 text-red-400 text-[9px] font-bold px-2 py-0.5 rounded border border-red-500/30">Review</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-col items-end pl-2">
                                <div class="text-sm font-bold {{ $tx->arus == 'In' ? 'text-emerald-500' : 'text-white' }} tracking-tight mb-1">
                                    {{ $tx->arus == 'In' ? '+' : '-' }}Rp {{ number_format($tx->nominal, 0, ',', '.') }}
                                </div>
                                <button wire:click="editTransaction('{{ $tx->id }}')" class="p-1 text-gray-500 hover:text-[#cbfc1b] bg-white/5 rounded-md transition-colors">
                                    <i data-lucide="edit-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
            
<div class="mt-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="text-lg font-semibold text-white">
                Recent Transactions
            </h3>
            <p class="text-xs text-gray-400">
                {{ number_format($transactions->total()) }} total records
            </p>
        </div>

        <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#cbfc1b]/10 border border-[#cbfc1b]/20">
            <div class="w-2 h-2 rounded-full bg-[#cbfc1b] shadow-[0_0_8px_#cbfc1b]"></div>
            <span class="text-xs font-medium text-[#cbfc1b]">
                Page {{ $transactions->currentPage() }}
            </span>
        </div>
    </div>

    <div class="rounded-3xl border border-white/10 bg-white/[0.03] backdrop-blur-xl p-4">
        <div class="flex flex-wrap items-center justify-between gap-4">

            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto justify-center sm:justify-start">
                {{-- Previous --}}
                @if ($transactions->onFirstPage())
                    <span class="px-3 py-2 rounded-2xl bg-white/5 text-gray-600 text-xs sm:text-sm cursor-not-allowed">
                        Prev
                    </span>
                @else
                    <button type="button" wire:click="previousPage"
                       class="px-3 py-2 rounded-2xl bg-white/5 hover:bg-white/10 border border-white/10 text-white text-xs sm:text-sm transition">
                        Prev
                    </button>
                @endif

                {{-- Page Numbers --}}
                @foreach ($transactions->getUrlRange(1, $transactions->lastPage()) as $page => $url)
                    @if ($page == $transactions->currentPage())
                        <span class="w-8 h-8 sm:w-10 sm:h-10 text-xs sm:text-sm rounded-xl sm:rounded-2xl bg-[#cbfc1b] text-black font-bold flex items-center justify-center shadow-[0_0_20px_rgba(203,252,27,0.35)]">
                            {{ $page }}
                        </span>
                    @elseif(
                        $page == 1 ||
                        $page == $transactions->lastPage() ||
                        abs($page - $transactions->currentPage()) <= 1
                    )
                        <button type="button" wire:click="gotoPage({{ $page }})"
                           class="w-8 h-8 sm:w-10 sm:h-10 text-xs sm:text-sm rounded-xl sm:rounded-2xl bg-white/5 hover:bg-white/10 border border-white/10 text-white flex items-center justify-center transition">
                            {{ $page }}
                        </button>
                    @elseif(
                        $page == $transactions->currentPage() - 2 ||
                        $page == $transactions->currentPage() + 2
                    )
                        <span class="text-gray-500 px-1">
                            ...
                        </span>
                    @endif
                @endforeach

                {{-- Next --}}
                @if ($transactions->hasMorePages())
                    <button type="button" wire:click="nextPage"
                       class="px-3 py-2 rounded-2xl bg-white/5 hover:bg-white/10 border border-white/10 text-white text-xs sm:text-sm transition">
                        Next
                    </button>
                @else
                    <span class="px-3 py-2 rounded-2xl bg-white/5 text-gray-600 text-xs sm:text-sm cursor-not-allowed">
                        Next
                    </span>
                @endif
            </div>

            <div class="hidden md:flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10">
                <i data-lucide="list" class="w-4 h-4 text-[#cbfc1b]"></i>
                <span class="text-xs text-gray-400">
                    Showing
                    <span class="text-white">{{ $transactions->firstItem() ?? 0 }}</span>
                    -
                    <span class="text-white">{{ $transactions->lastItem() ?? 0 }}</span>
                    of
                    <span class="text-white">{{ number_format($transactions->total()) }}</span>
                </span>
            </div>

        </div>
    </div>
</div>
        @endif
    </div>

    <!-- Edit Modal (Bottom Sheet) -->
    <div x-show="isEditModalOpen" style="display: none;" class="relative z-50">
        <div x-show="isEditModalOpen" x-transition.opacity class="fixed inset-0 bg-gray-900/50 max-w-md mx-auto"></div>
        <div 
            x-show="isEditModalOpen" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-y-full"
            x-transition:enter-end="translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="translate-y-0"
            x-transition:leave-end="translate-y-full"
            class="fixed bottom-0 left-0 right-0 w-full max-w-md mx-auto bg-[#111111] rounded-t-3xl shadow-[0_-10px_40px_rgba(0,0,0,0.5)] z-50 pb-8 pt-4 px-6 border-t border-white/10 max-h-[90vh] overflow-y-auto">
            
            <div class="w-12 h-1.5 bg-white/20 rounded-full mx-auto mb-6"></div>
            
            <h3 class="text-lg font-bold text-white mb-6">Edit Transaction</h3>

            <form wire:submit.prevent="saveEdit" class="space-y-4">
                
                <div>
                    <label class="block mb-1 text-xs font-bold text-gray-400">Merchant / Title</label>
                    <input type="text" wire:model="editMerchant" class="bg-[#1c1c1c] border border-white/10 text-white text-sm font-bold rounded-xl focus:ring-[#cbfc1b] focus:border-[#cbfc1b] block w-full p-3">
                </div>

                <div class="flex gap-3">
                    <div class="w-1/2">
                        <label class="block mb-1 text-xs font-bold text-gray-400">Type (In/Out)</label>
                        <select wire:model="editArus" class="bg-[#1c1c1c] border border-white/10 text-white text-sm font-bold rounded-xl focus:ring-[#cbfc1b] focus:border-[#cbfc1b] block w-full p-3">
                            <option value="In">In (+)</option>
                            <option value="Out">Out (-)</option>
                        </select>
                    </div>
                    <div class="w-1/2">
                        <label class="block mb-1 text-xs font-bold text-gray-400">Amount (Rp)</label>
                        <input type="number" wire:model="editNominal" class="bg-[#1c1c1c] border border-white/10 text-white text-sm font-bold rounded-xl focus:ring-[#cbfc1b] focus:border-[#cbfc1b] block w-full p-3">
                    </div>
                </div>

                <div>
                    <label class="block mb-1 text-xs font-bold text-gray-400">Category</label>
                    <select wire:model="editKategori" class="bg-[#1c1c1c] border border-white/10 text-white text-sm font-bold rounded-xl focus:ring-[#cbfc1b] focus:border-[#cbfc1b] block w-full p-3">
                        <option value="Uncategorized">Uncategorized</option>
                        <option value="Food & Beverage">Food & Beverage</option>
                        <option value="Transportation">Transportation</option>
                        <option value="Online Shopping">Online Shopping</option>
                        <option value="Groceries">Groceries</option>
                        <option value="Healthcare">Healthcare</option>
                        <option value="Entertainment">Entertainment</option>
                        <option value="Transfer Out">Transfer Out</option>
                        <option value="E-Wallet Top Up">E-Wallet Top Up</option>
                        <option value="Side Hustle">Side Hustle</option>
                        <option value="Other Income">Other Income</option>
                        <option value="Investment">Investment</option>
                        <option value="Others">Others</option>
                        @foreach($availableCategories as $cat)
                            @if(!in_array($cat, ['Uncategorized','Food & Beverage','Transportation','Online Shopping','Groceries','Healthcare','Entertainment','Transfer Out','E-Wallet Top Up','Side Hustle','Other Income','Investment','Others']))
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                <div class="flex gap-3">
                    <div class="w-1/2">
                        <label class="block mb-1 text-xs font-bold text-gray-400">Date & Time</label>
                        <input type="datetime-local" wire:model="editTanggal" class="bg-[#1c1c1c] border border-white/10 text-white text-sm font-bold rounded-xl focus:ring-[#cbfc1b] focus:border-[#cbfc1b] block w-full p-3">
                    </div>
                    <div class="w-1/2">
                        <label class="block mb-1 text-xs font-bold text-gray-400">Source (Bank)</label>
                        <input type="text" wire:model="editSumber" class="bg-[#1c1c1c] border border-white/10 text-white text-sm font-bold rounded-xl focus:ring-[#cbfc1b] focus:border-[#cbfc1b] block w-full p-3">
                    </div>
                </div>

                <p class="text-[10px] text-[#cbfc1b] mt-2 mb-4 bg-[#cbfc1b]/10 p-2 rounded-lg border border-[#cbfc1b]/20 text-center font-medium">
                    <i data-lucide="lock" class="w-3 h-3 inline mr-1 relative -top-0.5"></i>
                    Saving this will lock it locally. Future syncs will not overwrite these changes.
                </p>

                <div class="flex gap-3 pt-2">
                    <button type="button" @click="isEditModalOpen = false" class="flex-1 py-3 px-4 text-sm font-bold text-white bg-transparent rounded-xl border border-white/20 hover:bg-white/10 transition-colors">Cancel</button>
                    <button type="submit" class="flex-1 text-black bg-[#cbfc1b] hover:bg-[#b5e016] focus:ring-4 focus:outline-none focus:ring-[#cbfc1b]/50 font-bold rounded-xl text-sm px-4 py-3 text-center shadow-md transition-colors">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>
