<div>
    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        @php
            $kategori_master = ['Retail', 'Restoran', 'Pulsa', 'Kredit'];
        @endphp

        @php
            $path = request()->path();
            $pageMaster = explode('/', $path);
            $master = ['category', 'customer', 'supplier', 'product', 'brand', 'credit-customer'];
        @endphp
        <li class="nav-item ">
            <a href="{{ route('business.dashboard', $business_id) }}" class="nav-link @if($pageMaster[1] == 'dashboard') active @endif">Dashboard</a> 
        </li>  
        @if (in_array($kategori, $kategori_master))
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-nav dropdown-toggle @if(in_array($pageMaster[1], $master)) active @endif" href="#" id="navbarDropdown" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    Master
                </a>

                <div class="dropdown-menu dropdown-menu-start" aria-labelledby="navbarDropdown">     
                    
                    {{-- Menu Kategori --}}
                    @php
                        $kategori_kategori = ['Retail', 'Restoran'];
                    @endphp       
                    @if (in_array($kategori, $kategori_kategori))
                        <div>
                            <a class="dropdown-item @if($pageMaster[1] == 'category') active @endif" href="{{ route('business.category.index', $business_id) }}">
                                Kategori
                            </a>
                        </div> 
                    @endif   

                    {{-- Menu Brand --}}
                    @php
                        $kategori_brand = ['Retail'];
                    @endphp
                    @if (in_array($kategori, $kategori_brand))
                        <div>
                            <a class="dropdown-item @if($pageMaster[1] == 'brand') active @endif" href="{{ route('business.brand.index', $business_id) }}">
                                Brand
                            </a>
                        </div> 
                    @endif
                    
                    {{-- Menu Pemasok --}}
                    <div>
                        <a class="dropdown-item @if($pageMaster[1] == 'supplier') active @endif" href="{{ route('business.supplier.index', $business_id) }}">
                            Supplier
                        </a>
                    </div> 

                    {{-- Menu Pelanggan Biasa --}}
                    @php
                        $kategori_pelanggan = ['Retail', 'Restoran', 'Pulsa']
                    @endphp
                    @if (in_array($kategori, $kategori_pelanggan ))
                        <div>
                            <a class="dropdown-item @if($pageMaster[1] == 'customer') active @endif" href="{{ route('business.customer.index', $business_id) }}">
                                Pelanggan
                            </a>
                        </div> 
                    @endif

                    {{-- Menu Pelanggan Kredit --}}
                    @php
                        $kategori_pelanggan_kredit = ['Kredit']
                    @endphp
                    @if (in_array($kategori, $kategori_pelanggan_kredit ))
                        <div>
                            <a class="dropdown-item @if($pageMaster[1] == 'credit_customer') active @endif" href="#">
                                Pelanggan Kredit
                            </a>
                        </div> 
                    @endif

                    {{-- Menu Produk --}}
                    @php
                        $kategori_produk = ['Restoran']
                    @endphp
                    @if (in_array($kategori, $kategori_produk ))
                        <div>
                            <a class="dropdown-item @if($pageMaster[1] == 'product') active @endif" href="{{ route('business.product.index', $business_id) }}">
                                Produk
                            </a>
                        </div> 
                    @endif
                </div>
            </li>
        @endif

        {{-- Menu Produk --}}
        @php
            $kategori_stock = ['Retail'];
            $product = ['incoming-item', 'stock'];
        @endphp

        @if (in_array($kategori, $kategori_stock ))
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-nav dropdown-toggle @if(in_array($pageMaster[1], $product)) active @endif" href="#" id="navbarDropdown" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    Produk
                </a>
                <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="navbarDropdown">
                    <li>
                        <a class="dropdown-item @if($pageMaster[1] == 'incoming-item') active @endif" href="{{ route('business.incoming-item.index', $business_id) }}">
                            Barang Masuk
                        </a>
                    </li> 
                    <li>
                        <a class="dropdown-item @if($pageMaster[1] == 'stock') active @endif" href="{{ route('business.stock.index', $business_id) }}">
                            Stok
                        </a>
                    </li> 
                </ul>
            </li>
        @endif

        <li class="nav-item ">
            <a href="{{ route('business.asset.index', $business_id) }}" class="nav-link @if($pageMaster[1] == 'asset') active @endif">Asset</a> 
        </li>     

        @php
            $kategori_harian = ['Retail', 'Restoran', 'Pulsa'];
            $harian = ['cashier', 'daily-incomes', 'daily-outcomes', 'account-receivable', 'pay-later'];
        @endphp
        
        @if (in_array($kategori, $kategori_harian))
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-nav dropdown-toggle @if(in_array($pageMaster[1], $harian)) active @endif" href="#" id="navbarDropdown" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    Harian
                </a>
                <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="navbarDropdown">
                        <li>
                            <a 
                                class="dropdown-item 
                                        @if($pageMaster[1] == 'cashier') 
                                            active 
                                        @endif" 
                                        
                                        href="{{ route('business.cashier.index', $business_id) }}">
                                Kasir
                            </a>
                        </li> 

                        @if ($kategori == 'Retail')
                            <li>
                                <a 
                                    class="dropdown-item 
                                            @if($pageMaster[1] == 'account-receivable') 
                                                active 
                                            @endif" 
                                            href="{{ route('business.account-receivable.index', $business_id) }}">
                                    Piutang
                                </a>
                            </li> 
                        @endif
                        
                        @if ($kategori == 'Restoran')
                            <li>
                                <a 
                                    class="dropdown-item 
                                            @if($pageMaster[1] == 'pay-later') 
                                                active 
                                            @endif" 
                                            href="{{ route('business.account-receivable.pay-later', $business_id) }}">
                                    Belum Bayar
                                </a>
                            </li> 
                        @endif
                        
                        <li>
                            <a 
                                class="dropdown-item 
                                        @if($pageMaster[1] == 'daily-incomes') 
                                            active 
                                        @endif" 
                                        href="{{ route('business.daily-income.index', $business_id) }}">
                                Pendapatan Harian
                            </a>
                        </li>                     
                </ul>
            </li>
        @endif

        @php
            $kategori_keuangan = ['Retail', 'Restoran'];
            $keuangan = ['income', 'expense', 'business-income'];
        @endphp

        @if (in_array($kategori, $kategori_keuangan))
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-nav dropdown-toggle @if(in_array($pageMaster[1], $keuangan)) active @endif" href="#" id="navbarDropdown" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    Keuangan
                </a>
                <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="navbarDropdown">
                    {{-- @if ($kategori == 'Retail') --}}
                        <li>
                            <a 
                                class="dropdown-item 
                                        @if($pageMaster[1] == 'business-income') 
                                            active 
                                        @endif" 
                                        
                                        href="{{ route('business.business-income.index', $business_id) }}">
                                Uang Masuk
                            </a>
                        </li> 
                        <li>
                            <a 
                                class="dropdown-item 
                                        @if($pageMaster[1] == 'expense') 
                                            active 
                                        @endif" 
                                        href="{{ route('business.expense.index', $business_id) }}">
                                Uang Keluar
                            </a>
                        </li> 
                        
                    {{-- @endif --}}
                </ul>
            </li>
        @endif

    </ul>
</div>