<div>
    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        @php
            $kategori_master = ['Retail', 'Restoran', 'Pulsa', 'simpan-pinjam', 'lainnya'];
        @endphp

        @php
            $path = request()->path();
            $pageMaster = explode('/', $path);
            $master = ['contact', 'fixed-asset', 'supplier', 'product', 'brand', 'credit-customer'];
        @endphp
        <li class="nav-item ">
            <a href="{{ route('business.dashboard', $id) }}" class="nav-link @if($pageMaster[1] == 'dashboard') active @endif">Dashboard</a> 
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
                        $kategori_kategori = ['Retail', 'Restoran', 'simpan-pinjam', 'lainnya'];
                    @endphp       
                    @if (in_array($kategori, $kategori_kategori))
                        <div>
                            <a class="dropdown-item @if($pageMaster[1] == 'contact') active @endif" href="{{ route('business.contact.index', $id) }}">
                                Kontak
                            </a>
                        </div> 
                        <div>
                            <a class="dropdown-item @if($pageMaster[1] == 'fixed-asset') active @endif" href="{{ route('business.fixed-asset.index', $id) }}">
                                Harta Tetap
                            </a>
                        </div> 
                        <div>
                            <a class="dropdown-item @if($pageMaster[1] == 'product') active @endif" href="{{ route('business.product.index', $id) }}">
                                Produk
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
                </div>
            </li>
        @endif

        {{-- Buku Besar --}}
        @php
            $ledger = ['account', 'journal', 'ledger'];
        @endphp
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-nav dropdown-toggle @if(in_array($pageMaster[1], $ledger)) active @endif" href="#" id="navbarDropdown" role="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                Akuntansi
            </a>

            <div class="dropdown-menu dropdown-menu-start" aria-labelledby="navbarDropdown">     
                <div>
                    <a class="dropdown-item @if($pageMaster[1] == 'account') active @endif" href="{{ route('business.account.index', $id) }}">
                        Akun
                    </a>
                    <a class="dropdown-item @if($pageMaster[1] == 'journal') active @endif" href="{{ route('business.journal.index', $id) }}">
                        Jurnal
                    </a>
                    <a class="dropdown-item @if($pageMaster[1] == 'ledger') active @endif" href="{{ route('business.ledger.index', $id) }}">
                        Buku Besar
                    </a>
                </div> 
            </div>
        </li>

        {{-- Menu Produk --}}
        @php
            $kategori_stock = ['Retail', 'lainnya'];
            $product = ['inventory-adjustment', 'stock-opname'];
        @endphp

        @if (in_array($kategori, $kategori_stock ))
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-nav dropdown-toggle @if(in_array($pageMaster[1], $product)) active @endif" href="#" id="navbarDropdown" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    Persediaan Barang
                </a>
                <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="navbarDropdown">
                    <li>
                        <a class="dropdown-item @if($pageMaster[1] == 'inventory-adjustment') active @endif" href="{{ route('business.inventory-adjustment.index', $id) }}">
                            Penyesuaian Barang
                        </a>
                    </li> 
                    <li>
                        <a class="dropdown-item @if($pageMaster[1] == 'stock-opname') active @endif" href="{{ route('business.stock-opname.index', $id) }}">
                            Stok Opname
                        </a>
                    </li> 
                </ul>
            </li>
        @endif  

        {{-- Tabungan --}}
        @php
            $kategori_harian = ['simpan-pinjam'];
            $harian = ['saving-account', 'withdrawal', 'deposit'];
        @endphp
        
        @if (in_array($kategori, $kategori_harian))
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-nav dropdown-toggle @if(in_array($pageMaster[1], $harian)) active @endif" href="#" id="navbarDropdown" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    Tabungan
                </a>
                <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="navbarDropdown">
                    <li>
                        <a 
                            class="dropdown-item 
                            @if($pageMaster[1] == 'saving-account') 
                                active 
                            @endif" 
                            
                            href="{{ route('business.saving-account.index', $id) }}">
                            Nasabah
                        </a>
                    </li>                     
                    <li>
                        <a 
                            class="dropdown-item 
                            @if($pageMaster[1] == 'deposit') 
                                active 
                            @endif" 
                            
                            href="{{ route('business.deposit.index', $id) }}">
                            Setor Tunai
                        </a>
                    </li>  
                    <li>
                        <a 
                            class="dropdown-item 
                            @if($pageMaster[1] == 'withdrawal') 
                                active 
                            @endif" 
                            
                            href="{{ route('business.withdrawal.index', $id) }}">
                            Tarik Tunai
                        </a>
                    </li>  
                </ul>
            </li>
        @endif

        @php
            $kategori_harian = ['lainnya', 'simpan-pinjam'];
            $harian = ['invoice', 'account-receivable', 'account-receivable-payment', 'debt-submission', 'credit-application', 'lend', 'credit-sales', 'over-due'];
        @endphp
        
        @if (in_array($kategori, $kategori_harian))
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-nav dropdown-toggle @if(in_array($pageMaster[1], $harian)) active @endif" href="#" id="navbarDropdown" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    @if ($kategori == 'simpan-pinjam')
                        Pinjaman / Kredit
                    @else
                        Penjualan 
                    @endif
                </a>
                <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="navbarDropdown">
                        @if ($kategori == 'lainnya')
                            <li>
                                <a 
                                    class="dropdown-item 
                                            @if($pageMaster[1] == 'invoice') 
                                                active 
                                            @endif" 
                                            
                                            href="{{ route('business.invoice.index', $id) }}">
                                    Faktur Penjualan
                                </a>
                            </li> 
                        @endif

                        @if ($kategori == 'simpan-pinjam')
                            <li>
                                <a 
                                    class="dropdown-item 
                                            @if($pageMaster[1] == 'debt-submission') 
                                                active 
                                            @endif" 
                                            
                                            href="{{ route('business.debt-submission.index', $id) }}">
                                    Pengajuan Pinjaman
                                </a>
                            </li> 
                            <li>
                                <a 
                                    class="dropdown-item 
                                            @if($pageMaster[1] == 'lend') 
                                                active 
                                            @endif" 
                                            
                                            href="{{ route('business.lend.index', $id) }}">
                                    Pemberian Pinjaman
                                </a>
                            </li> 
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a 
                                    class="dropdown-item 
                                            @if($pageMaster[1] == 'credit-application') 
                                                active 
                                            @endif" 
                                            
                                            href="{{ route('business.credit-application.index', $id) }}">
                                    Pengajuan Kredit
                                </a>
                            </li> 
                            <li>
                                <a 
                                    class="dropdown-item 
                                            @if($pageMaster[1] == 'credit-sales') 
                                                active 
                                            @endif" 
                                            
                                            href="{{ route('business.credit-sales.index', $id) }}">
                                    Pemberian Kredit
                                </a>
                            </li> 
                            <li><hr class="dropdown-divider"></li>
                        @endif
                        
                        <li>
                            <a 
                                class="dropdown-item 
                                        @if($pageMaster[1] == 'account-receivable') 
                                            active 
                                        @endif" 
                                        href="{{ route('business.account-receivable.index', $id) }}">
                                Daftar Piutang
                            </a>
                        </li>                    
                        <li>
                            <a 
                                class="dropdown-item 
                                        @if($pageMaster[1] == 'account-receivable-payment') 
                                            active 
                                        @endif" 
                                        href="{{ route('business.account-receivable-payment.index', $id) }}">
                                Pembayaran Piutang
                            </a>
                        </li>      
                        <li>
                            <a 
                                class="dropdown-item 
                                    @if($pageMaster[1] == 'over-due') 
                                        active 
                                    @endif" 
                                    href="{{ route('business.over-due.index', $id) }}">
                                Jatuh Tempo
                            </a>
                        </li>                   
                </ul>
            </li>
        @endif

        @php
            $kategori_harian = ['Retail', 'Restoran', 'simpan-pinjam', 'lainnya'];
            $harian = ['purchase-goods', 'daily-incomes', 'daily-outcomes', 'account-payable', 'account-payable-payment', 'pay-later'];
        @endphp
        
        @if (in_array($kategori, $kategori_harian))
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-nav dropdown-toggle @if(in_array($pageMaster[1], $harian)) active @endif" href="#" id="navbarDropdown" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    Pembelian
                </a>
                <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="navbarDropdown">
                        <li>
                            <a 
                                class="dropdown-item 
                                        @if($pageMaster[1] == 'purchase-goods') 
                                            active 
                                        @endif" 
                                        
                                        href="{{ route('business.purchase-goods.index', $id) }}">
                                Faktur Pembelian
                            </a>
                        </li> 
                        <li>
                            <a 
                                class="dropdown-item 
                                        @if($pageMaster[1] == 'account-payable') 
                                            active 
                                        @endif" 
                                        href="{{ route('business.account-payable.index', $id) }}">
                                Daftar Utang
                            </a>
                        </li> 
                        
                        <li>
                            <a 
                                class="dropdown-item 
                                        @if($pageMaster[1] == 'account-payable-payment') 
                                            active 
                                        @endif" 
                                        href="{{ route('business.account-payable-payment.index', $id) }}">
                                Pembayaran Utang
                            </a>
                        </li>                     
                </ul>
            </li>
        @endif

        @php
            $kategori_keuangan = ['Retail', 'simpan-pinjam', 'lainnya'];
            $keuangan = ['revenue', 'expense', 'cash-mutation'];
        @endphp

        @if (in_array($kategori, $kategori_keuangan))
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-nav dropdown-toggle @if(in_array($pageMaster[1], $keuangan)) active @endif" href="#" id="navbarDropdown" role="button"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    Arus Kas
                </a>
                <ul class="dropdown-menu dropdown-menu-start" aria-labelledby="navbarDropdown">
                    {{-- @if ($kategori == 'Retail') --}}
                        <li>
                            <a 
                                class="dropdown-item 
                                @if($pageMaster[1] == 'revenue') 
                                    active 
                                @endif" 
                                
                                href="{{ route('business.revenue.index', $id) }}">
                                Pendapatan
                            </a>
                        </li> 
                        <li>
                            <a 
                                class="dropdown-item 
                                @if($pageMaster[1] == 'expense') 
                                    active 
                                @endif" 
                                href="{{ route('business.expense.index', $id) }}">
                                Pengeluaran
                            </a>
                        </li> 
                        <li>
                            <a 
                                class="dropdown-item 
                                @if($pageMaster[1] == 'cash-mutation') 
                                    active 
                                @endif" 
                                href="{{ route('business.cash-mutation.index', $id) }}">
                                Mutasi Kas
                            </a>
                        </li> 
                    {{-- @endif --}}
                </ul>
            </li>
        @endif

        {{-- Buku Besar --}}
        @php
            $ledger = ['report'];
        @endphp
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-nav dropdown-toggle @if(in_array($pageMaster[1], $ledger)) active @endif" href="#" id="navbarDropdown" role="button"
                data-bs-toggle="dropdown" aria-expanded="false">
                Laporan
            </a>

            <div class="dropdown-menu dropdown-menu-start" aria-labelledby="navbarDropdown">     
                <div>
                    <a class="dropdown-item @if($pageMaster[1] == 'report') @if($pageMaster[2] == 'cashflow') active @endif @endif" href="{{ route('report.business.cashflow.index', $id) }}">
                        Arus Kas
                    </a>
                    <a class="dropdown-item @if($pageMaster[1] == 'report') @if($pageMaster[2] == 'cashflow-year') active @endif @endif" href="{{ route('report.business.cashflow.year', $id) }}">
                        Arus Kas Tahunan
                    </a>
                    <a><hr class="dropdown-divider"></a>
                    <a class="dropdown-item @if($pageMaster[1] == 'report') @if($pageMaster[2] == 'balance') active @endif @endif" href="{{ route('report.business.balance.index', $id) }}">
                        Neraca
                    </a>
                    <a class="dropdown-item @if($pageMaster[1] == 'report') @if($pageMaster[2] == 'balance-year') active @endif @endif" href="{{ route('report.business.balance.year', $id) }}">
                        Neraca Tahunan
                    </a>
                    <a><hr class="dropdown-divider"></a>
                    <a class="dropdown-item @if($pageMaster[1] == 'report')  @if($pageMaster[2] == 'lost-profit') active @endif @endif" href="{{ route('report.business.lost-profit.index', $id) }}">
                        Laba Rugi
                    </a>
                    <a class="dropdown-item @if($pageMaster[1] == 'report')  @if($pageMaster[2] == 'lost-profit-year') active @endif @endif" href="{{ route('report.business.lost-profit.year', $id) }}">
                        Laba Rugi Tahunan
                    </a>
                    <a><hr class="dropdown-divider"></a>
                    <a class="dropdown-item @if($pageMaster[1] == 'report')  @if($pageMaster[2] == 'changes-in-equity') active @endif @endif" href="{{ route('report.business.changes-in-equity.index', $id) }}">
                        Perubahan Modal
                    </a>
                    <a class="dropdown-item @if($pageMaster[1] == 'report')  @if($pageMaster[2] == 'trial-balance') active @endif @endif" href="{{ route('report.business.trial-balance.index', $id) }}">
                        Neraca Saldo
                    </a>
                </div> 
            </div>
        </li>
    </ul>
</div>