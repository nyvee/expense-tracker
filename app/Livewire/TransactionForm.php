<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;

class TransactionForm extends Component
{
    public $tanggal;
    public $nominal;
    public $arus = 'Out'; // Out / In / Transfer
    public $merchant;
    public $kategori = '';
    public $sumber = 'Cash';
    public $transfer_ke = 'BCA'; // For Transfer mode

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
        $this->tanggal = date('Y-m-d\TH:i');
    }

    public function save()
    {
        if ($this->arus === 'Transfer') {
            $this->validate([
                'tanggal' => 'required|date',
                'nominal' => 'required|numeric|min:1',
                'merchant' => 'required|string',
                'sumber' => 'required|string',
                'transfer_ke' => 'required|string|different:sumber',
            ]);

            // Transaction 1: Transfer Out from Source
            $tx1 = Transaction::create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'tanggal' => \Carbon\Carbon::parse($this->tanggal)->toISOString(),
                'nominal' => $this->nominal,
                'arus' => 'Out',
                'merchant' => $this->merchant,
                'kategori' => 'Transfer Out',
                'sumber' => $this->sumber,
                'status' => 'Reviewed',
                'is_manually_edited' => true, // lock it so sync doesn't overwrite
            ]);
            $this->pushToGoogleSheets($tx1);

            // Transaction 2: Transfer In to Target
            $tx2 = Transaction::create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'tanggal' => \Carbon\Carbon::parse($this->tanggal)->toISOString(),
                'nominal' => $this->nominal,
                'arus' => 'In',
                'merchant' => $this->merchant,
                'kategori' => 'Transfer In',
                'sumber' => $this->transfer_ke,
                'status' => 'Reviewed',
                'is_manually_edited' => true,
            ]);
            $this->pushToGoogleSheets($tx2);

        } else {
            $this->validate([
                'tanggal' => 'required|date',
                'nominal' => 'required|numeric|min:1',
                'arus' => 'required|in:In,Out',
                'merchant' => 'required|string',
                'kategori' => 'required|string',
                'sumber' => 'required|string',
            ]);

            $tx = Transaction::create([
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'tanggal' => \Carbon\Carbon::parse($this->tanggal)->toISOString(),
                'nominal' => $this->nominal,
                'arus' => $this->arus,
                'merchant' => $this->merchant,
                'kategori' => $this->kategori,
                'sumber' => $this->sumber,
                'status' => 'Reviewed',
                'is_manually_edited' => true,
            ]);
            $this->pushToGoogleSheets($tx);
        }

        $this->reset(['nominal', 'merchant', 'kategori']);
        $this->dispatch('close-tx-modal');
        $this->dispatch('transaction-added');
        return redirect()->to('/');
    }

    private function pushToGoogleSheets($tx)
    {
        $url = config('services.google_sheets.api_url');
        if ($url) {
            try {
                // Fire and forget (in a real app you'd queue this)
                Http::timeout(5)->post($url, [
                    'id' => $tx->id,
                    'tanggal' => $tx->tanggal,
                    'nominal' => $tx->nominal,
                    'arus' => $tx->arus,
                    'merchant' => $tx->merchant,
                    'kategori' => $tx->kategori,
                    'sumber' => $tx->sumber
                ]);
            } catch (\Exception $e) {
                // Ignore failure, it's saved locally
            }
        }
    }

    public function render()
    {
        return view('livewire.transaction-form');
    }
}
