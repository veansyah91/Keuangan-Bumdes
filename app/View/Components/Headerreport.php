<?php

namespace App\View\Components;

use App\Models\Identity;
use Illuminate\View\Component;

class Headerreport extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $identity = Identity::first();
        return view('components.header-report', compact('identity'));
    }
}
