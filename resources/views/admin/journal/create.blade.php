@extends('layouts.admin')

@section('admin')
    <div class="page-heading d-flex justify-content-between my-auto">
        <h3>Tambah Jurnal</h3>
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ route('journal.index') }}">Jurnal</a></li>
              <li class="breadcrumb-item active" aria-current="page">Tambah Jurnal</li>
            </ol>
        </nav>
    </div>

    <div class="page-content">
        <div class="card">
            <div class="card-body">
                <div class="alert alert-success d-flex align-items-center d-none" id="success-alert" role="alert">
                    <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                    <div id="success-alert-message">
                    </div>
                </div>
                <div class="row">
                    
                    <div class="col-12 col-lg-4">
                        <div class="mb-3">
                            <label for="date-input" class="form-label fw-bold">Tanggal</label>
                            <input type="date" class="form-control" id="date-input" placeholder="Tanggal" onchange="changeDataInput(this)">
                            
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="mb-3">
                            <label for="description-input" class="form-label fw-bold">Deskripsi</label>
                            <input type="text" class="form-control" id="description-input" placeholder="Deskripsi" onchange="getNoRefAccountAuto(this)">
                            
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="mb-3">
                            <label for="no-ref-input" class="form-label fw-bold">No Referensi</label>
                            <input type="text" class="form-control" id="no-ref-input" placeholder="Kode Akun" onchange="noRefChange(this)">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="mb-3">
                            <label for="detail-input" class="form-label fw-bold">Detail (optional) </label>
                            <input type="text" class="form-control" id="detail-input" placeholder="Detail" onchange="detailInputChange(this)">
                        </div>
                    </div>
                </div>

                {{-- list input jurnal  --}}
                {{-- Header --}}
                <div class="row justify-content-between border-top pt-2 border-bottom pb-2 fw-bold">
                    <div class="col-4">
                        Akun
                    </div>
                    <div class="col-3 text-end">
                        Debit
                    </div>
                    <div class="col-3 text-end">
                        Kredit
                    </div>
                    <div class="col-1">
                        #
                    </div>
                </div>

                <div id="list-input-content">
                    
                </div>

                <div class="button-add-list">
                    <button class="btn btn-secondary w-100" onclick="addListInput()">Tambah</button>
                </div>

                <div class="row justify-content-between pt-2 border-top mt-2 pb-2 font-bold">
                    <div class="col-4 text-end">
                        Total
                    </div>
                    <div class="col-3 text-end" id="total-debit">
                        Rp. 0
                    </div>
                    <div class="col-3 text-end" id="total-credit">
                        Rp. 0
                    </div>
                    <div class="col-1">
                        
                    </div>
                </div>

                <div class="row justify-content-between pt-2 border-bottom pb-2 font-bold">
                    <div class="col-4 text-end">
                        Selisih
                    </div>
                    <div class="col-3 text-end">
                        
                    </div>
                    <div class="col-3 text-end" id="difference-debit-credit">
                        Rp. 0
                    </div>
                    <div class="col-1">
                        
                    </div>
                </div>
                <div class="row justify-content-end pt-2 border-bottom pb-2 font-bold">
                    <div class="col-lg-2 col-6 text-end">
                        <a class="btn btn-outline-primary w-100" href="{{ route('journal.index') }}">Batal</a>
                    </div>  
                    <div class="col-lg-2 col-6 text-end d-none" id="btn-submit-journal">
                        <button class="btn btn-primary w-100" onclick="submitJournal()" id="btn-submit">
                            <span id="submit-button-label">
                                Simpan
                            </span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="/js/admin/journal/api.js"></script>
    <script src="/js/admin/journal/create22052023.js"></script>
@endsection
