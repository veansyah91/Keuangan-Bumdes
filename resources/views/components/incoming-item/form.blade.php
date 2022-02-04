<div>
    <div class="d-flex justify-content-center text-center">
        <div class="col-12 fw-bold">
            Data Nota
        </div>
    </div>
    <div class="row justify-content-center mt-2">
        <div class="col-12">
            <div class="mb-3">
                <label for="nomor" class="form-label">Nomor Nota</label>
                <input type="text" class="form-control nomor" id="nomor" name="nomor" aria-describedby="nomorHelp" value="{{ $incomingItem['nomor_nota'] }}" required>
            </div>
        </div>
        <div class="col-12">
            <div class="mb-3">
                <label for="tanggal-nota" class="form-label">Tanggal Nota</label>
                <input type="date" class="form-control tanggal-nota" id="tanggal-nota" name="tanggal_nota" aria-describedby="tanggal-notaHelp" value="{{ $incomingItem['tanggal_nota'] }}" required>
            </div>
        </div>
        <div class="col-12">
            <div class="mb-3">
                <label for="tanggal-masuk" class="form-label">Tanggal Masuk</label>
                <input type="date" class="form-control tanggal-masuk" id="tanggal-masuk" name="tanggal_masuk" aria-describedby="tanggal-masukHelp" value="{{ $incomingItem['tanggal_masuk'] }}" required>
            </div>
        </div>
    </div>
    <hr>
</div>