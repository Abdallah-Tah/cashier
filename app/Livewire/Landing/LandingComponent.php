<?php

namespace App\Livewire\Landing;

use Livewire\Component;

class LandingComponent extends Component
{
    public $email = '';
    public $darkMode = false;

    public function mount()
    {
        // Check system dark mode preference
        $this->darkMode = false;
    }

    public function toggleDarkMode()
    {
        $this->darkMode = !$this->darkMode;
    }

    public function startFreeTrial()
    {
        $this->validate([
            'email' => 'required|email'
        ]);

        return redirect()->route('register', ['email' => $this->email]);
    }

    public function render()
    {
        return view('livewire.landing.landing-component');
    }
}
