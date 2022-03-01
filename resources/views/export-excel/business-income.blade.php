<table>
    <thead>
        <tr class="text-center">
            <th>No</th>
            <th>Tanggal</th>
            <th>Barang</th>
            @if ($business['kategori'] == 'Retail')
                <th>Modal</th>
            @endif
            <th>Jual</th>
            <th>Qty</th>
            <th>Jumlah</th>
            @if ($business['kategori'] == 'Retail')
                <th>Laba</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @if ($invoices->isNotEmpty())
            @php
                $i = 1;
                $totalPenjualan = 0;
                $totalLaba = 0;

                $tanggalAwal = '';
            @endphp
            @foreach ($invoices as $invoice)
                @foreach ($invoice->products as $product)
                    @if ($product->pivot->harga > 10)
                        <tr>
                            @if ($tanggalAwal == $invoice->created_at->toDateString())
                                <td></td>
                                <td></td>
                            @else 
                                <td>{{ $i++ }}</td>
                                <td>{{ $invoice->created_at->toDateString() }}</td>
                                @php
                                    $tanggalAwal = $invoice->created_at->toDateString()
                                @endphp
                            @endif
                            
                            <td>{{ $product->nama_produk }}</td>
                            @if ($business['kategori'] == 'Retail')
                                <td>{{ $product->modal }}</td>
                            @endif
                            <td>{{ $product->pivot->harga }}</td>
                            <td>{{ $product->pivot->jumlah }}</td>
                            @php
                                $totalJual = $product->pivot->harga * $product->pivot->jumlah;
                                $totalModal = $product->modal * $product->pivot->jumlah;

                                $totalPenjualan += $totalJual;
                                $totalLaba += $totalJual - $totalModal;
                            @endphp
                            <td>{{ $totalJual }}</td>
                            @if ($business['kategori'] == 'Retail')
                                <td>{{ $totalJual - $totalModal }}</td>
                            @endif
                            
                        </tr>
                    @endif
                @endforeach
            @endforeach

            <tr>
                <th 
                    @if ($business['kategori'] == 'Retail')
                        colspan="6" 
                    @else
                        colspan="5" 
                    @endif
                > 
                    <strong>Total</strong>                            
                </th>
                <th>
                    {{ $totalPenjualan }}
                </th>
                @if ($business['kategori'] == 'Retail')
                    <th>
                        {{ $totalLaba }}
                    </th>
                @endif
                
            </tr>
        @else
            <tr class="text-center">
                <td colspan="8">
                    <i>Data Kosong</i>
                </td>
            </tr>
        @endif
    </tbody>
</table>
