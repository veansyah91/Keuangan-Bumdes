<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Income Report</title>
    <style>
        .text-center{
            text-align:center
        }

        .text-right{
            text-align:right
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
    <center>
        <h2>{{ $identity['nama_desa'] }}</h2>
        <h4>Kecamatan {{ $identity['nama_kecamatan'] }}, {{ $identity['nama_kabupaten'] }}, {{ $identity['nama_provinsi'] }}</h4>
    </center>
    <center>
        <h2>Laporan Uang Masuk</h2>

        <table class="border">
            <thead>
                <tr class="text-center">
                    <th class="border">Tanggal</th>
                    <th class="border">Keterangan</th>
                    <th class="border">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @if ($incomes->isNotEmpty())
                    @foreach ($incomes as $income)
                        <tr class="text-center">
                            <td class="text-center border">{{ $income->tanggal_masuk }}</td>
                            <td class="text-center border">{{ $income->keterangan }}</td>
                            <td class="text-right border">Rp. {{ number_format($income->jumlah,0,",",".") }}</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="2" class="text-center border" style="font-size: 20px"> 
                            <strong>Jumlah</strong>                            
                        </td>
                        <td class="text-right border" style="font-size: 20px">
                            {{ number_format($total,0,",",".") }}
                        </td>
                    </tr>
                @else
                    <tr class="text-center">
                        <td colspan="5">
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