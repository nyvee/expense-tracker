<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;

class PinScreenComponent extends Component
{
    public $pin = '';
    public $error = false;

    public function updatedPin()
    {
        $this->error = false;

        // Automatically verify when 6 digits are entered
        if (strlen($this->pin) >= 6) {
            $this->verify();
        }
    }

    public function verify()
    {
        $expectedPin = env('APP_PIN', '123456');

        if ($this->pin === $expectedPin) {
            // Set cookie for 1 year (525600 minutes)
            cookie()->queue('pwa_unlocked', true, 525600);
            return redirect()->route('dashboard');
        } else {
            $this->error = true;
            $this->pin = ''; // Reset pin on failure
            $this->js("if(navigator.vibrate) navigator.vibrate(200);"); // Haptic feedback on failure
        }
    }

    public function appendDigit($digit)
    {
        if (strlen($this->pin) < 6) {
            $this->pin .= $digit;
            $this->updatedPin();
        }
    }

    public function removeDigit()
    {
        if (strlen($this->pin) > 0) {
            $this->pin = substr($this->pin, 0, -1);
            $this->error = false;
        }
    }

    #[Layout('layouts.guest')]
    public function render()
    {
        return view('livewire.pin-screen-component');
    }
}
