<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Asset;
use Livewire\Attributes\Layout;

class ProfileComponent extends Component
{
    public $assets;
    
    // Form fields for new asset
    public $name = '';
    public $type = 'Savings';
    public $balance = '';
    public $target_balance = '';
    
    public $isEditMode = false;
    public $editId = null;

    // Settings
    public $cycle_start_date;
    public $cycle_end_date;
    public $monthly_budget;

    public function mount()
    {
        $this->loadAssets();
        $this->loadSettings();
    }

    public function loadSettings()
    {
        $this->cycle_start_date = \App\Services\FinanceCycleService::getSetting('cycle_start_date', 25);
        $this->cycle_end_date = \App\Services\FinanceCycleService::getSetting('cycle_end_date', '');
        $this->monthly_budget = \App\Services\FinanceCycleService::getSetting('monthly_budget', 5000000);
    }

    public function saveSettings()
    {
        $this->validate([
            'cycle_start_date' => 'required|numeric|min:1|max:31',
            'cycle_end_date' => 'nullable|numeric|min:1|max:31',
            'monthly_budget' => 'required|numeric|min:0'
        ]);

        \App\Services\FinanceCycleService::setSetting('cycle_start_date', $this->cycle_start_date);
        \App\Services\FinanceCycleService::setSetting('cycle_end_date', $this->cycle_end_date ?: null);
        \App\Services\FinanceCycleService::setSetting('monthly_budget', $this->monthly_budget);

        $this->js("alert('Settings saved successfully!');");
        $this->dispatch('settings-updated');
    }

    public function loadAssets()
    {
        $this->assets = Asset::orderBy('type')->orderBy('name')->get();
    }

    public function saveAsset()
    {
        $this->validate([
            'name' => 'required|string',
            'type' => 'required|in:Savings,Investment,Gold',
            'balance' => 'required|numeric|min:0',
            'target_balance' => 'nullable|numeric|min:0',
        ]);

        if ($this->isEditMode && $this->editId) {
            $asset = Asset::find($this->editId);
            if ($asset) {
                $asset->update([
                    'name' => $this->name,
                    'type' => $this->type,
                    'balance' => $this->balance,
                    'target_balance' => $this->target_balance ?: null,
                ]);
            }
        } else {
            Asset::create([
                'name' => $this->name,
                'type' => $this->type,
                'balance' => $this->balance,
                'target_balance' => $this->target_balance ?: null,
            ]);
        }

        $this->resetForm();
        $this->loadAssets();
        $this->dispatch('close-asset-modal');
    }

    public function editAsset($id)
    {
        $asset = Asset::find($id);
        if ($asset) {
            $this->isEditMode = true;
            $this->editId = $asset->id;
            $this->name = $asset->name;
            $this->type = $asset->type;
            $this->balance = $asset->balance;
            $this->target_balance = $asset->target_balance;
            
            $this->dispatch('open-asset-modal');
        }
    }
    
    public function deleteAsset($id)
    {
        Asset::destroy($id);
        $this->loadAssets();
    }

    public function resetForm()
    {
        $this->isEditMode = false;
        $this->editId = null;
        $this->name = '';
        $this->balance = '';
        $this->target_balance = '';
        $this->type = 'Savings';
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.profile-component');
    }
}
