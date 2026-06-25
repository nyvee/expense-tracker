<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;

class DashboardComponent extends Component
{
    public $totalIn = 0;
    public $totalOut = 0;
    public $needsReviewCount = 0;
    public $latestTransactions = [];
    public $lastSynced = 'Never';
    
    public $balancesBySource = [];
    public $cycleInfo = [];
    public $spentThisCycle = 0;
    public $safeToSpendDaily = 0;
    public $safeToSpendWeekly = 0;
    public $remainingBudget = 0;
    public $monthlyComparison = [];
    public $spendingCategories = [];

    // Cycle Filter
    public $monthOffset = 0;

    // Analytics Properties
    public $totalWealth = 0;
    public $netWorthChange = 0;
    public $netWorthChangePct = 0;
    public $dailySpendingTrend = [];
    public $largestTransaction = null;
    public $savingsRate = 0;
    public $burnRate = 0;

    public function mount()
    {
        $this->lastSynced = \Illuminate\Support\Facades\Cache::get('last_synced_at', 'Never');
        $this->loadData();
    }

    #[On('settings-updated')]
    public function loadData()
    {
        $this->cycleInfo = \App\Services\FinanceCycleService::getCurrentCycle($this->monthOffset);
        $startDate = $this->cycleInfo['start_date']->format('Y-m-d H:i:s');
        $endDate = $this->cycleInfo['end_date']->format('Y-m-d H:i:s');

        // Pemasukan & Pengeluaran Siklus Ini (Exclude Transfers)
        $this->totalIn = Transaction::where('arus', 'In')
            ->where('kategori', '!=', 'Transfer In')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->sum('nominal');

        $this->totalOut = Transaction::where('arus', 'Out')
            ->whereNotIn('kategori', ['Transfer Out', 'Investment'])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->sum('nominal');

        // Pengeluaran aktual (di luar Transfer dan Investasi) untuk Budgeting
        $this->spentThisCycle = Transaction::where('arus', 'Out')
            ->whereNotIn('kategori', ['Transfer Out', 'Investment'])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->sum('nominal');

        // Budgeting Math
        $this->remainingBudget = $this->cycleInfo['monthly_budget'] - $this->spentThisCycle;
        $this->safeToSpendDaily = $this->remainingBudget / $this->cycleInfo['remaining_days'];
        $this->safeToSpendWeekly = $this->safeToSpendDaily * 7;

        // Karantina Count
        $this->needsReviewCount = Transaction::where('status', 'Needs Review')->count();

        // Transaksi Terbaru
        $this->latestTransactions = Transaction::orderBy('tanggal', 'desc')->take(5)->get();

        // Saldo per Bank
        $balances = Transaction::select('sumber', DB::raw("SUM(CASE WHEN arus='In' THEN nominal ELSE -nominal END) as balance"))
            ->groupBy('sumber')
            ->get();
            
        // Default balances
        $this->balancesBySource = [
            'BCA' => 0,
            'BLU' => 0,
            'Line Bank' => 0,
            'Cash' => 0,
        ];

        // Menggabungkan sumber yang mirip (misal: "BLU Virtual Card" menjadi "BLU")
        foreach($balances as $b) {
            $sumber = explode(' ', $b->sumber)[0]; // Ambil kata pertama
            
            // Map "Line" to "Line Bank"
            if ($sumber == 'Line') {
                $sumber = 'Line Bank';
            }

            if ($sumber == 'Gopay') {
                continue;
            }

            if(!isset($this->balancesBySource[$sumber])) {
                $this->balancesBySource[$sumber] = 0;
            }
            $this->balancesBySource[$sumber] += $b->balance;
        }

        // Total Wealth
        $this->totalWealth = array_sum($this->balancesBySource);

        // Net Worth Change (In - Out for this cycle)
        $this->netWorthChange = $this->totalIn - $this->totalOut;
        if ($this->totalWealth > 0) {
            $this->netWorthChangePct = ($this->netWorthChange / ($this->totalWealth - $this->netWorthChange)) * 100;
        }

        // Savings Rate
        if ($this->totalIn > 0) {
            $this->savingsRate = (($this->totalIn - $this->totalOut) / $this->totalIn) * 100;
        }

        // Burn Rate (Average daily spending this cycle)
        $daysPassed = now()->diffInDays(\Carbon\Carbon::parse($startDate)) + 1;
        $this->burnRate = $this->spentThisCycle / max(1, $daysPassed);

        // Largest Transaction
        $this->largestTransaction = Transaction::where('arus', 'Out')
            ->whereNotIn('kategori', ['Transfer Out', 'Investment'])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('nominal', 'desc')
            ->first();

        // Daily Spending Trend (Last 7 Days within Cycle)
        $this->dailySpendingTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            // Only include if date is within cycle
            if ($date >= \Carbon\Carbon::parse($startDate)->format('Y-m-d') && $date <= \Carbon\Carbon::parse($endDate)->format('Y-m-d')) {
                $spent = Transaction::where('arus', 'Out')
                    ->whereNotIn('kategori', ['Transfer Out', 'Investment'])
                    ->whereDate('tanggal', $date)
                    ->sum('nominal');
                $this->dailySpendingTrend[] = [
                    'day' => now()->subDays($i)->format('D'),
                    'amount' => $spent
                ];
            }
        }

        // Komparasi Bulanan (SQLite uses strftime)
        $this->monthlyComparison = Transaction::select(
            DB::raw("strftime('%Y-%m', tanggal) as month"),
            DB::raw("SUM(CASE WHEN arus='In' THEN nominal ELSE 0 END) as income"),
            DB::raw("SUM(CASE WHEN arus='Out' THEN nominal ELSE 0 END) as expense")
        )
        ->groupBy('month')
        ->orderBy('month', 'desc')
        ->take(4) // 4 bulan terakhir
        ->get();

        // Spending Categories (Out only) this cycle
        $this->spendingCategories = Transaction::select('kategori', DB::raw('COUNT(*) as tx_count'), DB::raw('SUM(nominal) as total'))
            ->where('arus', 'Out')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->groupBy('kategori')
            ->orderBy('total', 'desc')
            ->take(4)
            ->get();
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.dashboard-component');
    }

    public function nextCycle()
    {
        $this->monthOffset++;
        $this->loadData();
    }

    public function prevCycle()
    {
        $this->monthOffset--;
        $this->loadData();
    }

    public function syncData()
    {
        try {
            $syncService = app(\App\Services\GoogleSheetsSyncService::class);
            $count = $syncService->sync();
            \Illuminate\Support\Facades\Cache::put('last_synced_at', now()->format('d M Y H:i:s'));
            $this->lastSynced = \Illuminate\Support\Facades\Cache::get('last_synced_at');
            $this->loadData();
            $this->js("alert('Sync success! Imported/Updated $count transactions.')");
        } catch (\Exception $e) {
            $this->js("alert('Sync failed: " . addslashes($e->getMessage()) . "')");
        }
    }
}
