<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Transaction;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

class HistoryComponent extends Component
{
    use WithPagination;

    public $startDate = '';
    public $endDate = '';
    public $typeFilter = ''; // In, Out
    public $categoryFilter = '';
    public $availableCategories = [];

    // Edit Modal State
    public $isEditModalOpen = false;
    public $editTxId = null;
    public $editNominal = 0;
    public $editMerchant = '';
    public $editKategori = '';
    public $editTanggal = '';
    public $editSumber = '';
    public $editArus = 'Out';

    public function mount()
    {
        // Default to current financial cycle
        $cycleInfo = \App\Services\FinanceCycleService::getCurrentCycle();
        $this->startDate = $cycleInfo['start_date']->format('Y-m-d');
        $this->endDate = $cycleInfo['end_date']->format('Y-m-d');

        // Fetch available categories
        $this->availableCategories = Transaction::select('kategori')
            ->whereNotNull('kategori')
            ->where('kategori', '!=', '')
            ->distinct()
            ->pluck('kategori')
            ->toArray();
    }

    public function updating($field)
    {
        // Reset pagination when filter changes
        if (in_array($field, ['startDate', 'endDate', 'typeFilter', 'categoryFilter'])) {
            $this->resetPage();
        }
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $query = Transaction::query();

        if ($this->startDate && $this->endDate) {
            $query->whereBetween('tanggal', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59']);
        }

        if ($this->typeFilter) {
            $query->where('arus', $this->typeFilter);
        }

        if ($this->categoryFilter) {
            $query->where('kategori', $this->categoryFilter);
        }

        $transactions = $query->orderBy('tanggal', 'desc')->paginate(15);

        return view('livewire.history-component', [
            'transactions' => $transactions
        ]);
    }

    public function editTransaction($id)
    {
        $tx = Transaction::find($id);
        if ($tx) {
            $this->editTxId = $tx->id;
            $this->editNominal = $tx->nominal;
            $this->editMerchant = $tx->merchant;
            $this->editKategori = $tx->kategori;
            $this->editTanggal = \Carbon\Carbon::parse($tx->tanggal)->format('Y-m-d\TH:i');
            $this->editSumber = $tx->sumber;
            $this->editArus = $tx->arus;
            
            $this->isEditModalOpen = true;
            $this->dispatch('open-edit-modal');
        }
    }

    public function saveEdit()
    {
        $this->validate([
            'editNominal' => 'required|numeric',
            'editMerchant' => 'required|string',
            'editTanggal' => 'required',
        ]);

        $tx = Transaction::find($this->editTxId);
        if ($tx) {
            $tx->update([
                'nominal' => $this->editNominal,
                'merchant' => $this->editMerchant,
                'kategori' => $this->editKategori,
                'tanggal' => \Carbon\Carbon::parse($this->editTanggal)->format('Y-m-d H:i:s'),
                'sumber' => $this->editSumber,
                'arus' => $this->editArus,
                'is_manually_edited' => true // Lock from future sync overwrites
            ]);

            $this->isEditModalOpen = false;
            $this->dispatch('close-edit-modal');
            $this->js("alert('Transaction successfully edited and locked from sync!');");
        }
    }
}
