<div>
    <table style="width: 100%; border-bottom: 2px solid black">
        <tr style="">
            <td style="width: 20%; text-align:center">
                <img src="{{ public_path($identity['image']) }}" alt="logo-kabupaten" style="width: 50%">
            </td>
            <td style="width: 60%; text-align:center;">
                <div style="font-weight: bold;font-size: 20px">
                    BADAN USAHA MILIK DESA (BUMDesa)
                </div>
                <div style="font-weight: bold;font-style: italic;font-size: 30px">
                    {{ $identity['nama_bumdes'] }}
                </div>
                <div>
                    DESA {{ strtoupper($identity['nama_desa']) }}, KECAMATAN {{ strtoupper($identity['nama_kecamatan']) }}
                </div>
                <div>
                    {{ ucwords($identity['alamat']) }}, Kode Pos {{ $identity['kode_pos'] }}
                </div>
                <div style="font-size: 12px">
                    No. Telp: {{ $identity['no_hp'] }} / Email: {{ $identity['email'] }}
                </div>
            </td>
            <td style="width: 20%; text-align:center">
                <img src="{{ public_path($identity['logo_usaha']) }}" alt="logo-bumdes" style="width: 70%">
            </td>
        </tr>
    </table>
</div>