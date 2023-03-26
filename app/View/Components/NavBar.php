<?php

namespace App\View\Components;

use Illuminate\View\View;
use Illuminate\View\Component;

class Navbar extends Component
{

    
    
    public function __construct(
        public string $kategori,
        public string $id
    )
    {}

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render(): View
    {
        
        return view('components.navbar');
    }
}
