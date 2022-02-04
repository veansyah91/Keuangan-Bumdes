<?php

namespace App\View\Components;

use Illuminate\View\Component;

class NavBar extends Component
{

    public $kategori;
    public $business_id;
    
    public function __construct($kategori, $id)
    {
        $this->kategori = $kategori;
        $this->business_id = $id;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.nav-bar');
    }
}
