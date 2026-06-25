<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;
use App\Models\MerchantRule;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;

class ReconciliationComponent extends Component
{
    public $transactions;
    
    // State untuk Bottom Sheet Modal
    public $activeTxId = null;
    public $activeMerchant = '';
    public $selectedCategory = '';
    public $newKeyword = '';
    public $rememberRule = true; // Toggle "Ingat aturan ini"

    // Daftar kategori standar
    public $categories = [
        'Food & Beverage',
        'Transportation',
        'Online Shopping',
        'Groceries',
        'Healthcare',
        'Entertainment',
        'Transfer Out',
        'E-Wallet Top Up',
        'Side Hustle',
        'Other Income',
        'Others'
    ];

    public function mount()
    {
        $this->loadTransactions();
    }

    public function loadTransactions()
    {
        $this->transactions = Transaction::where('status', 'Needs Review')
            ->orderBy('tanggal', 'desc')
            ->get();
            
        // Reset state
        $this->activeTxId = null;
        $this->activeMerchant = '';
        $this->selectedCategory = '';
        $this->newKeyword = '';
        $this->rememberRule = true;
    }
    
    public function openModal($id, $merchant)
    {
        $this->activeTxId = $id;
        $this->activeMerchant = $merchant;
        $this->newKeyword = $merchant; // Default keyword adalah nama persis
        $this->selectedCategory = '';
        $this->rememberRule = true;
    }

    public function saveRuleAndReview()
    {
        \Log::info('saveRuleAndReview called', [
            'activeTxId' => $this->activeTxId,
            'selectedCategory' => $this->selectedCategory,
            'newKeyword' => $this->newKeyword,
            'rememberRule' => $this->rememberRule
        ]);

        if (!$this->activeTxId || empty($this->selectedCategory)) {
            \Log::warning('Validation failed or empty values', [
                'activeTxId' => $this->activeTxId,
                'selectedCategory' => $this->selectedCategory
            ]);
            $this->js("alert('Please select a category before saving.')");
            return;
        }

        $tx = Transaction::find($this->activeTxId);
        if (!$tx) {
            \Log::error('Transaction not found', ['activeTxId' => $this->activeTxId]);
            $this->js("alert('Transaction not found. ID: ' + '{$this->activeTxId}')");
            return;
        }

        // Selalu update transaksi yang sedang di-review ini terlebih dahulu
        $tx->update([
            'kategori' => $this->selectedCategory,
            'status' => 'Reviewed',
            'is_manually_edited' => true
        ]);

        // Jika user mencentang Toggle "Ingat Aturan"
        if ($this->rememberRule && !empty($this->newKeyword)) {
            MerchantRule::firstOrCreate(
                ['keyword' => $this->newKeyword],
                ['kategori' => $this->selectedCategory]
            );

            // Terapkan ke semua antrean lain dengan keyword sama
            Transaction::where('status', 'Needs Review')
                ->where('id', '!=', $this->activeTxId)
                ->where('merchant', 'LIKE', '%' . $this->newKeyword . '%')
                ->update([
                    'kategori' => $this->selectedCategory,
                    'status' => 'Reviewed',
                    'is_manually_edited' => true
                ]);
        }

        $this->loadTransactions();
        
        // Memerintahkan Alpine.js untuk menutup Modal
        $this->dispatch('close-modal');
    }

    public function quickAction($id, $category)
    {
        $tx = Transaction::find($id);
        if (!$tx) return;

        // Langsung update tanpa buat rule (khusus ojol dll)
        $tx->update([
            'kategori' => $category,
            'status' => 'Reviewed',
            'is_manually_edited' => true
        ]);

        $this->loadTransactions();
    }

    public function isOjol($merchant)
    {
        return Str::contains(strtolower($merchant), ['grab', 'gojek', 'gopay']);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.reconciliation-component');
    }

    public function autoMatchTransfers()
    {
        $needsReview = Transaction::where('status', 'Needs Review')->get();
        $groupedByDate = $needsReview->groupBy(function($tx) {
            return \Carbon\Carbon::parse($tx->tanggal)->format('Y-m-d');
        });

        $matchCount = 0;
        $allowedFees = [0, 2500, 4000, 6500, 7500];

        foreach ($groupedByDate as $date => $transactions) {
            $outs = $transactions->where('arus', 'Out')->values();
            $ins = $transactions->where('arus', 'In')->values();

            foreach ($outs as $outTx) {
                foreach ($ins as $inKey => $inTx) {
                    $diff = $outTx->nominal - $inTx->nominal;

                    if ($diff >= 0 && in_array($diff, $allowedFees)) {
                        // Match found!
                        
                        // 1. Update In Transaction
                        $inTx->update([
                            'kategori' => 'Transfer In',
                            'status' => 'Reviewed'
                        ]);

                        // 2. Update Out Transaction
                        $outTx->update([
                            'nominal' => $inTx->nominal,
                            'kategori' => 'Transfer Out',
                            'status' => 'Reviewed'
                        ]);

                        // 3. Create Bank Fee Transaction if there is a difference
                        if ($diff > 0) {
                            Transaction::create([
                                'id' => (string) \Illuminate\Support\Str::uuid(),
                                'tanggal' => $outTx->tanggal,
                                'nominal' => $diff,
                                'arus' => 'Out',
                                'merchant' => 'Biaya Admin Transfer (' . $outTx->merchant . ')',
                                'sumber' => $outTx->sumber,
                                'kategori' => 'Bank Fee',
                                'status' => 'Reviewed'
                            ]);
                        }

                        // Remove this 'In' transaction from available pool to avoid double matching
                        $ins->forget($inKey);
                        $matchCount++;
                        break; // Move to next 'Out' transaction
                    }
                }
            }
        }

        $this->loadTransactions();
        
        if ($matchCount > 0) {
            $this->js("alert('Berhasil melakukan Auto-Match pada $matchCount pasang transfer!');");
        } else {
            $this->js("alert('Tidak ada transfer antar bank yang cocok ditemukan.');");
        }
    }
}
