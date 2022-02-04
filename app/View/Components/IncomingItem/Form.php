<?php

namespace App\View\Components\IncomingItem;

use App\Models\Incomingitem;
use Illuminate\View\Component;

class Form extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $incomingItem;

    public function __construct($incomingitemid)
    {
        $this->incomingItem = Incomingitem::find($incomingitemid);
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.incoming-item.form');
    }
}
