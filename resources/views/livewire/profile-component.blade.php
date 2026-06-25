<div class="p-4 pb-28 font-sans" x-data="{ openAssetModal: false }" @open-asset-modal.window="openAssetModal = true" @close-asset-modal.window="openAssetModal = false">
    <!-- Total Assets Summary -->
    @php
        $totalBalance = $assets->sum('balance');
        $totalTarget = $assets->sum('target_balance');
    @endphp
<div class="relative overflow-hidden rounded-[32px] bg-[#cbfc1b] p-6 mb-6 shadow-[0_20px_50px_rgba(203,252,27,0.18)]">

    <!-- Decorative Elements -->
    <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
    <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-black/10 rounded-full blur-2xl"></div>

    <!-- Header -->
    <div class="relative z-10 flex items-center justify-between">
        <div>

            <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-black/50">
                Total Assets
            </p>

            <h2 class="mt-2 text-[24px] leading-none font-semibold tracking-[-0.04em] text-black">
                Rp {{ number_format($totalBalance, 0, ',', '.') }}
            </h2>
        </div>

        <div class="w-12 h-12 rounded-2xl bg-black/10 backdrop-blur-md flex items-center justify-center">
            <i data-lucide="wallet" class="w-6 h-6 text-black"></i>
        </div>
    </div>

    @if($totalTarget > 0)
        @php
            $progress = min(($totalBalance / $totalTarget) * 100, 100);
        @endphp

        <div class="relative z-10 mt-6">

            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-medium text-black/60">
                    Target Progress
                </span>

                <span class="text-xs font-bold text-black">
                    {{ number_format($progress, 1) }}%
                </span>
            </div>

            <div class="h-2 rounded-full bg-black/10 overflow-hidden">
                <div
                    class="h-full rounded-full bg-black transition-all duration-700"
                    style="width: {{ $progress }}%">
                </div>
            </div>

            <div class="flex justify-between mt-3">
                <div>
                    <p class="text-[10px] text-black/50">
                        Current
                    </p>
                    <p class="text-sm font-semibold text-black">
                        Rp {{ number_format($totalBalance, 0, ',', '.') }}
                    </p>
                </div>

                <div class="text-right">
                    <p class="text-[10px] text-black/50">
                        Target
                    </p>
                    <p class="text-sm font-semibold text-black">
                        Rp {{ number_format($totalTarget, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    @endif
</div>

    <!-- Assets List Header -->
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-sm font-bold text-white">Asset Portfolio</h3>
        <button wire:click="resetForm(); $dispatch('open-asset-modal')" class="text-[10px] font-bold bg-[#cbfc1b]/10 text-[#cbfc1b] border border-[#cbfc1b]/30 px-3 py-1.5 rounded-full hover:bg-[#cbfc1b]/20 transition-colors">
            + Add Asset
        </button>
    </div>

    <!-- Assets List -->
    <div class="space-y-3">
        @forelse($assets as $asset)
            <div class="bg-[#1c1c1c] rounded-2xl p-4 shadow-sm border border-white/5 cursor-pointer hover:border-[#cbfc1b]/50 transition-colors" wire:click="editAsset({{ $asset->id }})">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0
                        @if($asset->type == 'Savings') bg-blue-500/10 text-blue-400
                        @elseif($asset->type == 'Investment') bg-emerald-500/10 text-emerald-400
                        @else bg-amber-500/10 text-amber-400 @endif
                    ">
                        @if($asset->type == 'Savings') <i data-lucide="piggy-bank" class="w-5 h-5"></i>
                        @elseif($asset->type == 'Investment') <i data-lucide="trending-up" class="w-5 h-5"></i>
                        @else <i data-lucide="coins" class="w-5 h-5"></i> @endif
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="text-[13px] font-bold text-white truncate">{{ $asset->name }}</h4>
                                <p class="text-[9px] font-bold text-gray-500 tracking-wider uppercase mt-0.5">{{ $asset->type }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-extrabold text-white">Rp {{ number_format($asset->balance, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        
                        @if($asset->target_balance > 0)
                        <div class="mt-2.5">
                            <div class="flex justify-between text-[9px] font-bold text-gray-500 mb-1">
                                <span>Target: Rp {{ number_format($asset->target_balance, 0, ',', '.') }}</span>
                                <span class="text-[#cbfc1b]">{{ number_format(($asset->balance / $asset->target_balance) * 100, 1) }}%</span>
                            </div>
                            <div class="w-full bg-[#111] rounded-full h-1 border border-white/5">
                                <div class="bg-[#cbfc1b] h-1 rounded-full" style="width: {{ min(($asset->balance / $asset->target_balance) * 100, 100) }}%"></div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-8 bg-[#1c1c1c] rounded-2xl border border-dashed border-white/10">
                <i data-lucide="wallet" class="w-10 h-10 mx-auto text-gray-600 mb-2"></i>
                <p class="text-xs font-bold text-gray-500">No assets recorded yet.</p>
            </div>
        @endforelse
    </div>

    <!-- App Settings -->
    <div class="mt-8 mb-4">
        <h3 class="text-sm font-bold text-white mb-4">App Settings</h3>
        <form wire:submit.prevent="saveSettings" class="bg-[#1c1c1c] rounded-[1.5rem] p-5 shadow-sm border border-white/5 space-y-4">
            
            <div class="grid grid-cols-2 gap-3">
                <!-- Cycle Start Date -->
                <div>
                    <label class="block mb-2 text-xs font-bold text-gray-300">Cycle Start</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none">
                            <i data-lucide="calendar" class="w-4 h-4 text-gray-500"></i>
                        </div>
                        <input type="number" wire:model="cycle_start_date" class="bg-black border border-white/10 text-white text-sm font-bold rounded-xl focus:ring-[#cbfc1b] focus:border-[#cbfc1b] block w-full ps-11 p-3.5 transition-colors" min="1" max="31">
                    </div>
                </div>

                <!-- Cycle End Date -->
                <div>
                    <label class="block mb-2 text-xs font-bold text-gray-300">Cycle End (Opt)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none">
                            <i data-lucide="calendar-off" class="w-4 h-4 text-gray-500"></i>
                        </div>
                        <input type="number" wire:model="cycle_end_date" placeholder="Auto" class="bg-black border border-white/10 text-white text-sm font-bold rounded-xl focus:ring-[#cbfc1b] focus:border-[#cbfc1b] block w-full ps-11 p-3.5 transition-colors" min="1" max="31">
                    </div>
                </div>
            </div>
            <p class="text-[10px] font-medium text-gray-500 mt-1">E.g., 25 for Start Date. If End Date is empty, the cycle automatically ends the day before the next start date.</p>

            <!-- Monthly Budget -->
            <div>
                <label class="block mb-2 text-xs font-bold text-gray-300">Global Monthly Budget (Rp)</label>
                <div class="relative">
                    <div class="absolute inset-y-0 start-0 flex items-center ps-4 pointer-events-none">
                        <span class="text-gray-500 font-bold">Rp</span>
                    </div>
                    <input type="number" wire:model="monthly_budget" class="bg-black border border-white/10 text-white text-sm font-bold rounded-xl focus:ring-[#cbfc1b] focus:border-[#cbfc1b] block w-full ps-11 p-3.5 transition-colors">
                </div>
                <p class="text-[10px] font-medium text-gray-500 mt-1">Used to calculate Safe-to-Spend daily allowance.</p>
            </div>

            <button type="submit" class="w-full text-black bg-[#cbfc1b] hover:bg-[#b5e016] focus:ring-4 focus:ring-[#cbfc1b]/50 font-bold rounded-xl text-sm px-4 py-3 mt-2 shadow-sm transition-colors">
                Save Settings
            </button>
        </form>
    </div>

    <!-- Asset Modal (Bottom Sheet) -->
    <div x-show="openAssetModal" style="display: none;" class="relative z-50">
        <div x-show="openAssetModal" x-transition.opacity class="fixed inset-0 bg-gray-900/50 max-w-md mx-auto"></div>

        <div x-show="openAssetModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full"
             class="fixed bottom-0 left-0 right-0 w-full max-w-md mx-auto bg-[#111111] rounded-t-3xl shadow-[0_-10px_40px_rgba(0,0,0,0.5)] z-50 pb-8 pt-4 px-6 border-t border-white/10 h-[85vh] overflow-y-auto">
            
            <div class="w-12 h-1.5 bg-white/20 rounded-full mx-auto mb-6"></div>
            
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-white">{{ $isEditMode ? 'Edit Asset' : 'Add Asset' }}</h3>
                <button @click="openAssetModal = false" class="text-gray-400 bg-transparent hover:bg-white/10 hover:text-white rounded-full text-sm w-8 h-8 ms-auto inline-flex justify-center items-center">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form wire:submit.prevent="saveAsset" class="space-y-4">
                <div>
                    <label class="block mb-1.5 text-xs font-bold text-white">Asset / Savings Name</label>
                    <input type="text" wire:model="name" required placeholder="e.g., Mutual Fund" class="bg-[#1c1c1c] border border-white/10 text-white text-sm font-medium rounded-xl focus:ring-[#cbfc1b] focus:border-[#cbfc1b] block w-full p-3 shadow-sm placeholder-gray-500">
                </div>

                <div>
                    <label class="block mb-1.5 text-xs font-bold text-white">Asset Type</label>
                    <select wire:model="type" required class="bg-[#1c1c1c] border border-white/10 text-white text-sm font-medium rounded-xl focus:ring-[#cbfc1b] focus:border-[#cbfc1b] block w-full p-3 shadow-sm">
                        <option value="Savings">Savings</option>
                        <option value="Investment">Investment</option>
                        <option value="Gold">Gold</option>
                    </select>
                </div>

                <div>
                    <label class="block mb-1.5 text-xs font-bold text-white">Current Balance (Rp)</label>
                    <input type="number" wire:model="balance" required placeholder="0" class="bg-[#1c1c1c] border border-white/10 text-white text-lg font-bold rounded-xl focus:ring-[#cbfc1b] focus:border-[#cbfc1b] block w-full p-3 shadow-sm placeholder-gray-500">
                </div>

                <div>
                    <label class="block mb-1.5 text-xs font-bold text-white">Target Balance (Optional)</label>
                    <input type="number" wire:model="target_balance" placeholder="0" class="bg-[#1c1c1c] border border-white/10 text-white text-sm font-medium rounded-xl focus:ring-[#cbfc1b] focus:border-[#cbfc1b] block w-full p-3 shadow-sm placeholder-gray-500">
                    <p class="text-[9px] text-gray-500 mt-1">Leave blank if no specific target.</p>
                </div>

                <div class="flex gap-3 pt-4">
                    @if($isEditMode)
                    <button type="button" wire:click="deleteAsset({{ $editId }}); openAssetModal = false" class="w-14 flex items-center justify-center text-rose-500 bg-rose-500/10 border border-rose-500/20 hover:bg-rose-500/20 rounded-xl transition-colors">
                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                    </button>
                    @endif
                    <button type="submit" class="flex-1 text-black bg-[#cbfc1b] hover:bg-[#b5e016] focus:ring-4 focus:outline-none focus:ring-[#cbfc1b]/50 font-bold rounded-xl text-sm px-5 py-3.5 text-center shadow-[0_8px_20px_rgb(203,252,27,0.3)] transition-colors">
                        Save Asset
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
