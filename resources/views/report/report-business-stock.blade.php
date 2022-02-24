<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Produk Unit Usaha</title>
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
        <h2>Laporan Stock</h2>

        <table class="border">
            <thead>
                <tr class="text-center">
                    <th class="border">Tanggal Masuk</th>
                    <th class="border">Kode</th>
                    <th class="border">Kategori</th>
                    <th class="border">Brand</th>
                    <th class="border">Pemasok</th>
                    <th class="border">Nama Produk</th>
                    <th class="border">Qty</th>
                    <th class="border">Modal</th>
                    <th class="border">Jual</th>
                    <th class="border">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @if ($products->isNotEmpty())
                    @foreach ($products as $product)
                        <tr class="text-center">
                            <td class="text-center border">{{ $product->created_at->toDateString() }}</td>
                            <td class="text-center border">{{ $product->kode }}</td>
                            <td class="text-center border">{{ strtoupper($product->kategori) }}</td>
                            <td class="text-center border">{{ strtoupper($product->brand) }}</td>
                            <td class="text-center border">{{ strtoupper($product->pemasok) }}</td>
                            <td class="text-center border">{{ strtoupper($product->nama_produk) }}</td>
                            <td class="text-center border">{{ $product->stock->jumlah }}</td>
                            <td class="text-right border">Rp. {{ number_format($product->modal,0,",",".") }}</td>
                            <td class="text-right border">Rp. {{ number_format($product->jual,0,",",".") }}</td>
                            <td class="text-right border">Rp. {{ number_format($product->modal * $product->stock->jumlah,0,",",".") }}</td>
                        </tr>
                    @endforeach

                    <tr>
                        <td colspan="9" class="text-center border" style="font-size: 20px"> 
                            <strong>Jumlah</strong>                            
                        </td>
                        <td class="text-right border" style="font-size: 20px">
                            {{ number_format($total,0,",",".") }}
                        </td>
                    </tr>
                @else
                    <tr class="text-center">
                        <td colspan="10">
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