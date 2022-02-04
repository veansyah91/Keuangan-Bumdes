<?php

namespace App\View\Components\Product;

use App\Models\Business;
use Illuminate\View\Component;

class Form extends Component
{
    public $business;
    public $pemasok;
    public $kategori;
    public $brand;
    public $page;

    public function __construct($businessid, $pemasok, $kategori, $brand, $page)
    {
        $this->business = Business::find($businessid);
        $this->pemasok = $pemasok;
        $this->kategori = $kategori;
        $this->brand = $brand;
        $this->page = $page;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.product.form');
    }
}
