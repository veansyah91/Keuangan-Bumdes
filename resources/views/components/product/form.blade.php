<div>
    <div class="row justify-content-center">
        <div class="col-12">
            <input type="text" class="page" hidden value="{{ $page }}" name="page">
            <div class="mb-3 @if ($business['kategori'] !== "Retail") d-none @endif">
                <label for="pemasok" class="form-label">Pemasok</label>
                <div class="input-group mb-3">
                    <button class="btn btn-outline-secondary cari-pemasok" type="button" data-business-id="{{ $business['id'] }}" data-bs-toggle="modal" data-bs-target="#cariPemasokModal">Cari</button>
                    <input type="text" class="form-control pemasok" name="pemasok" placeholder="" value="{{ $pemasok }}" aria-label="Example text with button addon" aria-describedby="button-addon1">
                </div>
            </div>

            <div class="mb-3">
                <label for="kategori" class="form-label">Kategori</label>
                <div class="input-group mb-3">
                    <button class="btn btn-outline-secondary cari-kategori" type="button" data-business-id="{{ $business['id'] }}" data-bs-toggle="modal" data-bs-target="#cariKategoriModal">Cari</button>
                    <input type="text" class="form-control kategori" name="kategori" aria-describedby="kategoriHelp" value="{{ $kategori }}" aria-label="Example text with button addon" aria-describedby="button-addon1">
                </div>
            </div>
            
            <div class="mb-3 @if ($business['kategori'] !== "Retail") d-none @endif">
                <label for="brand" class="form-label">Brand</label>
                <div class="input-group mb-3">
                    <button class="btn btn-outline-secondary cari-brand" type="button" data-business-id="{{ $business['id'] }}" data-bs-toggle="modal" data-bs-target="#cariBrandModal">Cari</button>
                    <input type="text" class="form-control brand" name="brand" aria-describedby="brandHelp" value="{{ $brand }}" aria-label="Example text with button addon" aria-describedby="button-addon1">
                </div>
            </div>

            <div class="mb-3">
                <label for="kode" class="form-label">Kode / SN</label>
                <input type="text" class="form-control kode" id="kode" name="kode" aria-describedby="kodeHelp" required>
            </div>

            <div class="mb-3">
                <label for="nama" class="form-label">Nama Produk</label>
                <input type="text" class="form-control nama" id="nama" name="nama" aria-describedby="namaHelp" value="{{ $brand }}" required>
            </div>                                

            <div class="mb-3 @if ($business['kategori'] !== "Retail") d-none @endif">
                <label for="modal" class="form-label">Modal</label>
                <input type="number" class="form-control modal-input" id="modal" name="modal" aria-describedby="modalHelp" required>
            </div>
            
            <div class="mb-3">
                <label for="jual" class="form-label">Jual</label>
                <input type="number" class="form-control jual" id="jual" name="jual" aria-describedby="jualHelp" required>
            </div>

            {{-- <button class="btn btn-primary save-button" data-business-id="{{ $business['id'] }}" type="button">Simpan</button> --}}
        </div>
    </div>
</div>