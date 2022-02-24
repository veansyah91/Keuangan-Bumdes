<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Penjualan Unit Usaha</title>
    <style>
        .text-center{
            text-align:center
        }

        .text-right{
            text-align:right
        }

        .text-left{
            text-align:left
        }

        table{
            width: 100%;
        }

        .border{
            border-collapse: collapse;
            border: 1px solid black;
            font-size: 12px;
        }

    </style>
</head>
<body>
    <x-headerreport />
    <center>
        <h4>{{ $business['nama'] }}</h4>
    </center>
    <center>
        <h2>Laporan Penjualan {{ $param }}</h2>

        <table class="border">
            <thead>
                <tr class="text-center">
                    <th class="border">No</th>
                    <th class="border">Tanggal</th>
                    <th class="border">Barang</th>
                    @if ($business['kategori'] == 'Retail')
                        <th class="border">Modal</th>
                    @endif
                    <th class="border">Jual</th>
                    <th class="border">Qty</th>
                    <th class="border">Jumlah</th>
                    @if ($business['kategori'] == 'Retail')
                        <th class="border">Laba</th>
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
                                        <td class="text-center border"></td>
                                        <td class="text-center border"></td>
                                    @else 
                                        <td class="text-center border">{{ $i++ }}</td>
                                        <td class="text-center border">{{ $invoice->created_at->toDateString() }}</td>
                                        @php
                                            $tanggalAwal = $invoice->created_at->toDateString()
                                        @endphp
                                    @endif
                                    
                                    <td class="text-left border">{{ $product->nama_produk }}</td>
                                    @if ($business['kategori'] == 'Retail')
                                        <td class="text-right border">Rp. {{ number_format($product->modal,0,",",".") }}</td>
                                    @endif
                                    <td class="text-right border">Rp. {{ number_format($product->pivot->harga,0,",",".") }}</td>
                                    <td class="text-center border">{{ $product->pivot->jumlah }}</td>
                                    @php
                                        $totalJual = $product->pivot->harga * $product->pivot->jumlah;
                                        $totalModal = $product->modal * $product->pivot->jumlah;

                                        $totalPenjualan += $totalJual;
                                        $totalLaba += $totalJual - $totalModal;
                                    @endphp
                                    <td class="text-right border">Rp. {{ number_format($totalJual,0,",",".") }}</td>
                                    @if ($business['kategori'] == 'Retail')
                                        <td class="text-right border">Rp. {{ number_format($totalJual - $totalModal,0,",",".") }}</td>
                                    @endif
                                    
                                </tr>
                            @endif
                        @endforeach
                    @endforeach

                    <tr>
                        <td 
                            @if ($business['kategori'] == 'Retail')
                                colspan="6" 
                            @else
                                colspan="5" 
                            @endif
                            
                            
                            class="text-center border" style="font-size: 20px"> 
                            <strong>Total</strong>                            
                        </td>
                        <td class="text-right border" style="font-size: 20px">
                            Rp. {{ number_format($totalPenjualan,0,",",".") }}
                        </td>
                        @if ($business['kategori'] == 'Retail')
                            <td class="text-right border" style="font-size: 20px">
                                Rp. {{ number_format($totalLaba,0,",",".") }}
                            </td>
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
    </center>

    <table style="margin-top:15px">
        <tr>
            <td style="width: 50%"></td>
            <td style="width: 50%" class="text-center">{{ ucwords($identity['nama_desa']) }}, {{ Date('d-m-Y') }}</td>
        </tr>
        <tr>
            <th style="width: 50%"></th>
            <th style="width: 50%" class="text-center">Direktur BUMDes</th>
        </tr>
        <tr>
            <td style="width: 50%"></td>
            <td style="width: 50%; height: 100px" class="text-center" style="vertical-align: text-bottom">{{ $identity->ketua }}</td>
        </tr>
    </table>
</body>
</html>