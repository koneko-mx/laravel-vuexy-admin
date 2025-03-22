<?php

namespace Koneko\VuexyAdmin\Livewire\Users;

use Koneko\VuexyAdmin\Models\User;

use Livewire\Component;

class UsersCount extends Component
{
    public $total, $enabled, $disabled;

    protected $listeners = ['refreshUsersCount' => 'updateCounts'];

    public function mount()
    {
        $this->updateCounts();
    }

    public function updateCounts()
    {
        $this->total = User::count();
        $this->enabled = User::where('status', User::STATUS_ENABLED)->count();
        $this->disabled = User::where('status', User::STATUS_DISABLED)->count();
    }

    public function render()
    {
        return view('vuexy-admin::livewire.users.count');
    }
}
