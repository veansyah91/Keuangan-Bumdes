<table>
    <thead>
    <tr>
        <th>Kode</th>
        <th>Kategori</th>
        <th>Brand</th>
        <th>Pemasok</th>
        <th>Nama Produk</th>
        <th>Qty</th>
        <th>Modal</th>
        <th>Jual</th>
        <th>Jumlah</th>

    </tr>
    </thead>
    <tbody>
    @foreach($products as $product)
        <tr>
            <td>{{ $product->kode }}</td>
            <td>{{ $product->kategori }}</td>
            <td>{{ $product->brand }}</td>
            <td>{{ $product->pemasok }}</td>
            <td>{{ $product->nama_produk }}</td>
            <td>{{ $product->stock->jumlah }} {{ $product->stock->satuan }}</td>
            <td>{{ $product->modal }}</td>
            <td>{{ $product->jual }}</td>
            <td>{{ $product->modal * $product->stock->jumlah }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
