<div class="p-5 pb-28 font-sans text-white">

    <!-- 1. Current Cycle Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <button wire:click="prevCycle" class="bg-white/10 hover:bg-white/20 rounded p-1 text-gray-300 transition-colors">
                    <i data-lucide="chevron-left" class="w-3.5 h-3.5"></i>
                </button>
                <h2 class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $monthOffset === 0 ? 'Current Cycle' : 'Past Cycle' }}</h2>
                <button wire:click="nextCycle" class="bg-white/10 hover:bg-white/20 rounded p-1 text-gray-300 transition-colors" {{ $monthOffset >= 0 ? 'disabled' : '' }}>
                    <i data-lucide="chevron-right" class="w-3.5 h-3.5 {{ $monthOffset >= 0 ? 'opacity-30' : '' }}"></i>
                </button>
            </div>
            <p class="text-sm font-bold text-white mt-1">
                {{ $cycleInfo['start_date']->format('d M') }} - {{ $cycleInfo['end_date']->format('d M Y') }}
            </p>
        </div>
        <div class="text-right flex items-center gap-2">
            <p class="text-[10px] text-gray-500 text-right mr-1">Left<br><span class="font-bold text-white">{{ ceil($cycleInfo['remaining_days']) }} days</span></p>
            <button wire:click="syncData" wire:loading.attr="disabled" class="bg-white/5 hover:bg-white/10 p-2.5 rounded-xl text-white transition-colors border border-white/10 flex items-center justify-center">
                <i data-lucide="refresh-cw" wire:loading.class="animate-spin" class="w-4 h-4"></i>
            </button>
        </div>
    </div>

    <!-- 2. Total Wealth Card -->
    <div class="mb-6 relative overflow-hidden rounded-[32px] bg-[#cbfc1b] p-6 shadow-[0_20px_50px_rgba(203,252,27,0.18)]">
        <!-- Decorative Elements -->
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-black/10 rounded-full blur-2xl"></div>

        <div class="relative z-10 flex flex-col items-center text-center">
            <!-- Status Pill -->
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-black/10 mb-4 border border-black/5">
                <div class="w-1.5 h-1.5 rounded-full {{ $netWorthChange >= 0 ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)]' : 'bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.8)]' }}"></div>
                <span class="text-[10px] font-bold text-black uppercase tracking-wide">
                    {{ $netWorthChange >= 0 ? 'Healthy Portfolio' : 'High Burn Rate' }}
                </span>
            </div>

            <p class="text-[11px] font-semibold uppercase tracking-[0.25em] text-black/50">
                Total Wealth
            </p>

            <h2 class="mt-2 text-[34px] leading-none font-bold tracking-[-0.04em] text-black drop-shadow-sm">
                Rp {{ number_format($totalWealth, 0, ',', '.') }}
            </h2>

            <div class="mt-4 flex items-center justify-center gap-1.5 text-xs font-bold text-black/70 bg-black/5 px-3 py-1.5 rounded-lg">
                @if($netWorthChange >= 0)
                    <i data-lucide="trending-up" class="w-3.5 h-3.5 text-emerald-600"></i>
                    <span class="text-emerald-700">+Rp {{ number_format($netWorthChange, 0, ',', '.') }}</span>
                @else
                    <i data-lucide="trending-down" class="w-3.5 h-3.5 text-rose-600"></i>
                    <span class="text-rose-700">-Rp {{ number_format(abs($netWorthChange), 0, ',', '.') }}</span>
                @endif
                <span class="text-black/40 ml-1">this cycle</span>
            </div>
        </div>
    </div>

    <!-- 3. Safe To Spend -->
    <div class="bg-[#1c1c1c] rounded-3xl p-5 mb-6 border border-white/5 relative overflow-hidden shadow-xl">
        <div class="flex justify-between items-start mb-4">
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Safe-to-Spend</p>
                <h2 class="text-2xl font-bold tracking-tight text-white">Rp {{ number_format($safeToSpendDaily, 0, ',', '.') }}<span class="text-sm font-medium text-gray-500">/day</span></h2>
            </div>
            <div class="text-right">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Monthly Left</p>
                <p class="text-sm font-bold text-[#cbfc1b]">Rp {{ number_format($remainingBudget, 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="pt-4 border-t border-white/10 flex justify-between items-center">
            <div>
                <p class="text-[10px] text-gray-500 font-medium">Burn Rate</p>
                <p class="text-xs font-bold text-white mt-0.5">Rp {{ number_format($burnRate, 0, ',', '.') }}/day</p>
            </div>
            <div class="text-right">
                <p class="text-[10px] text-gray-500 font-medium">Forecast</p>
                <p class="text-xs font-bold text-white mt-0.5">
                    @if($burnRate > 0)
                        {{ ceil($remainingBudget / $burnRate) }} days left
                    @else
                        Infinite
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- 4. Income vs Expense -->
    <div class="grid grid-cols-2 gap-3 mb-6">
        <div class="bg-emerald-500/10 border border-emerald-500/20 rounded-2xl p-4">
            <div class="w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center mb-2">
                <i data-lucide="arrow-down-left" class="w-3.5 h-3.5 text-emerald-500"></i>
            </div>
            <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest mb-1">Income</p>
            <p class="text-sm font-bold text-white">+Rp {{ number_format($totalIn, 0, ',', '.') }}</p>
        </div>
        <div class="bg-rose-500/10 border border-rose-500/20 rounded-2xl p-4">
            <div class="w-6 h-6 rounded-full bg-rose-500/20 flex items-center justify-center mb-2">
                <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 text-rose-500"></i>
            </div>
            <p class="text-[10px] font-bold text-rose-500 uppercase tracking-widest mb-1">Expenses</p>
            <p class="text-sm font-bold text-white">-Rp {{ number_format($totalOut, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- 5. Spending Trend Chart -->
    <div class="bg-[#1c1c1c] rounded-[1.5rem] p-5 mb-6 border border-white/5 shadow-xl">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-sm font-bold text-white">Spending Trend</h3>
            <span class="text-[10px] font-bold text-gray-500 bg-white/5 px-2 py-1 rounded-md">Last 7 Days</span>
        </div>
        <div class="flex items-end justify-between h-28 gap-2 pb-2 border-b border-white/5">
            @php
                $maxTrend = count($dailySpendingTrend) > 0 ? max(array_column($dailySpendingTrend, 'amount')) : 0;
            @endphp
            @foreach(array_reverse($dailySpendingTrend) as $trend)
                <div class="flex flex-col items-center w-full group relative">
                    <!-- Tooltip (Optional visual flair on hover) -->
                    <div class="absolute -top-8 bg-black text-white text-[9px] font-bold py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-10">
                        Rp {{ number_format($trend['amount']/1000, 0) }}k
                    </div>
                    
                    <div class="relative w-full flex justify-center h-[80px] items-end">
                        <div class="w-full max-w-[16px] rounded-t-sm transition-all duration-500 {{ $loop->last ? 'bg-[#cbfc1b]' : 'bg-white/20 group-hover:bg-white/40' }}" 
                             style="height: {{ $maxTrend > 0 ? max(5, ($trend['amount'] / $maxTrend) * 100) : 5 }}%">
                        </div>
                    </div>
                    <span class="text-[9px] font-medium {{ $loop->last ? 'text-[#cbfc1b]' : 'text-gray-500' }} mt-3 uppercase">{{ $trend['day'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 6. Wallet Distribution -->
    <div class="bg-[#1c1c1c] rounded-[1.5rem] p-5 mb-6 border border-white/5 shadow-xl">
        <h3 class="text-sm font-bold text-white mb-5">Wallet Distribution</h3>
        <div class="space-y-4">
            @foreach($balancesBySource as $bank => $balance)
                @php
                    $percentage = $totalWealth > 0 && $balance > 0 ? ($balance / $totalWealth) * 100 : 0;
                @endphp
                <div class="group">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-xs font-bold text-gray-300 group-hover:text-white transition-colors">{{ $bank }}</span>
                        <span class="text-[11px] font-bold text-white/50 group-hover:text-white transition-colors">{{ number_format($percentage, 0) }}% (Rp {{ number_format($balance, 0, ',', '.') }})</span>
                    </div>
                    <div class="w-full bg-black rounded-full h-1.5 overflow-hidden">
                        <div class="bg-white/80 h-full rounded-full transition-all duration-1000 ease-out" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 7. Top Spending Categories (Pie Chart) -->
    <div class="mb-6 bg-[#1c1c1c] rounded-[1.5rem] p-5 border border-white/5 shadow-xl">
        <h3 class="text-sm font-bold text-white mb-4">Top Categories</h3>
        
        @if($spendingCategories->isEmpty())
            <div class="text-center py-6">
                <p class="text-xs text-gray-500 font-medium">No spending data yet.</p>
            </div>
        @else
            @php 
                $totalCatSpending = $spendingCategories->sum('total');
                $colors = ['#cbfc1b', '#10b981', '#3b82f6', '#8b5cf6'];
                
                // Build conic gradient string
                $conicString = '';
                $currentPercentage = 0;
                
                foreach($spendingCategories as $index => $cat) {
                    $catPct = $totalCatSpending > 0 ? ($cat->total / $totalCatSpending) * 100 : 0;
                    $color = $colors[$index % count($colors)];
                    
                    $start = $currentPercentage;
                    $end = $currentPercentage + $catPct;
                    
                    $conicString .= "{$color} {$start}%, {$color} {$end}%" . ($index == count($spendingCategories) - 1 ? '' : ', ');
                    $currentPercentage = $end;
                }
            @endphp
            
            <div class="flex items-center gap-6">
                <!-- Donut Chart -->
                <div class="relative shrink-0">
                    <div class="w-24 h-24 rounded-full" style="background: conic-gradient({{ $conicString }});"></div>
                    <div class="absolute inset-0 m-auto w-16 h-16 bg-[#1c1c1c] rounded-full flex items-center justify-center shadow-inner">
                        <i data-lucide="pie-chart" class="w-5 h-5 text-gray-500"></i>
                    </div>
                </div>
                
                <!-- Legend -->
                <div class="space-y-3 flex-1">
                    @foreach($spendingCategories as $index => $cat)
                        @php 
                            $catPct = $totalCatSpending > 0 ? ($cat->total / $totalCatSpending) * 100 : 0; 
                            $color = $colors[$index % count($colors)];
                        @endphp
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full" style="background-color: {{ $color }}; shadow-[0_0_5px_{{ $color }}]"></div>
                                <span class="text-xs font-bold text-gray-200 truncate max-w-[80px]">{{ $cat->kategori }}</span>
                            </div>
                            <div class="text-right flex items-center gap-2">
                                <span class="text-[10px] text-gray-500">{{ number_format($catPct, 0) }}%</span>
                                <span class="text-xs font-bold text-white">Rp {{ number_format($cat->total / 1000, 0) }}k</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- 8. Largest Transaction -->
    @if($largestTransaction)
    <div class="mb-6">
        <h3 class="text-sm font-bold text-white mb-4 px-1">Largest Transaction</h3>
        <div class="bg-rose-500/10 border border-rose-500/20 rounded-2xl p-4 flex items-center justify-between relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-16 h-16 bg-rose-500/20 rounded-full blur-xl"></div>
            
            <div class="w-[60%] relative z-10">
                <p class="text-[9px] font-bold text-rose-500 uppercase tracking-widest mb-1.5">{{ \Carbon\Carbon::parse($largestTransaction->tanggal)->format('d M Y') }}</p>
                <p class="text-sm font-bold text-white truncate">{{ $largestTransaction->merchant }}</p>
                <p class="text-[10px] font-medium text-gray-400 mt-1">{{ $largestTransaction->kategori }} &middot; {{ $largestTransaction->sumber }}</p>
            </div>
            <div class="text-right relative z-10">
                <p class="text-[17px] font-bold text-rose-500 tracking-tight">-Rp {{ number_format($largestTransaction->nominal, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- 9. Needs Review -->
    @if($needsReviewCount > 0)
    <div class="mb-6">
        <a href="/reconciliation" class="bg-[#cbfc1b]/10 rounded-2xl p-4 flex items-center justify-between border border-[#cbfc1b]/30 hover:bg-[#cbfc1b]/20 transition-colors group">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-[#cbfc1b]/20 flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-[#cbfc1b]"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-[#cbfc1b] mb-0.5">Needs Review</h3>
                    <p class="text-[10px] font-medium text-[#cbfc1b]/70">{{ $needsReviewCount }} uncategorized transactions</p>
                </div>
            </div>
            <i data-lucide="chevron-right" class="w-5 h-5 text-[#cbfc1b] transform group-hover:translate-x-1 transition-transform"></i>
        </a>
    </div>
    @endif

</div>
