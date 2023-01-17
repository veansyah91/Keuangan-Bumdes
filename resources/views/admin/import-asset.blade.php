@extends('layouts.admin')

@section('admin')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Import Asset</h1>
    </div>

    <section class="border-top border-bottom">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-1">
            <h1 class="h4">Wilayah</h1>
        </div>

        @if (session('success'))
            <div class=" mt-3 alert alert-success d-flex align-items-center" role="alert">
                <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Success:"><use xlink:href="#check-circle-fill"/></svg>
                <div>
                    {{ session('success') }}
                </div>
            </div>
        @endif
    
        <div class="row">
            <div class="col-12 col-md-6">
                <ol class="list-group mb-2 pb-3">
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Provinsi</div>

                            @if (!$provinsi)
                            <form action="{{ route('import-asset.province') }}" method="POST" enctype="multipart/form-data" id="export-province-form">
                                    @csrf
                                    <input class="form-control @error('provinsiFile') is-invalid @enderror" type="file" id="provinsiFile" name="provinsiFile">
                            </form>
                            @else
                                <small class="fst-italic">Data Telah Diinput</small>
                            @endif
                            
                        </div>
                        @if ($provinsi)
                            <span class="fs-4 text-success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                    <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                                </svg>
                            </span>
                        @else
                            <button 
                                class="btn btn-success btn-sm my-auto"
                                onclick="document.getElementById('export-province-form').submit();">Import</button>
                        @endif
                        
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Kabupaten / Kota</div>
                            @if (!$kabupaten)
                                <form action="{{ route('import-asset.regency') }}" method="POST" enctype="multipart/form-data" id="export-regency-form">
                                    @csrf
                                    <input class="form-control @error('kabupatenFile') is-invalid @enderror" type="file" id="kabupatenFile" name="kabupatenFile">
                                </form>
                            @else
                                <small class="fst-italic">Data Telah Diinput</small>
                            @endif
                        </div>

                        @if ($kabupaten)
                            <span class="fs-4 text-success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                    <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                                </svg>
                            </span>
                        @else
                            <button 
                                class="btn btn-success btn-sm my-auto"
                                onclick="document.getElementById('export-regency-form').submit();"
                            >
                                Import
                            </button>
                        @endif
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Kecamatan</div>
                            @if (!$kecamatan)
                                <form action="{{ route('import-asset.district') }}" method="POST" enctype="multipart/form-data" id="export-district-form">
                                    @csrf
                                    <input class="form-control @error('kecamatanFile') is-invalid @enderror" type="file" id="kecamatanFile" name="kecamatanFile">
                                </form>
                            @else
                                <small class="fst-italic">Data Telah Diinput</small>
                            @endif
                        </div>
                        @if ($kecamatan)
                            <span class="fs-4 text-success">
                                <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                                    <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
                                </svg>
                            </span>
                        @else
                            <button 
                                class="btn btn-success btn-sm my-auto"
                                onclick="document.getElementById('export-district-form').submit();"
                            >
                                Import
                            </button>
                        @endif
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Desa / Kelurahan</div>
                                <form 
                                action="{{ route('import-asset.village') }}" 
                                method="POST" enctype="multipart/form-data" id="export-village-form" class="mb-2">
                                    @csrf
                                    <input class="form-control @error('desaFile') is-invalid @enderror" type="file" id="villageFile" name="desaFile">
                                    @error('desaFile')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </form>
                                @if ($desa)
                                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal" onclick="showVillage()">
                                            Lihat Data Desa
                                    </button>
                                @endif
                        </div>

                            <button 
                                class="btn btn-success btn-sm my-auto" 
                                onclick="document.getElementById('export-village-form').submit();"
                            >
                                Import
                            </button>
                        
                    </li>
                </ol>
            </div>
            
        </div>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row justify-content-end">
                            <div class="col-lg-6 col-6 text-end my-auto">
                                <small all class="fst-italic">
                                    <span id="count-data"> </span>
                                </small>
                            </div>
                            <div class="col-lg-3 col-3 text-end">
                                <button class="btn btn-sm" id="prev-page" onclick="prevButton()">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-double-left" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M8.354 1.646a.5.5 0 0 1 0 .708L2.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                                        <path fill-rule="evenodd" d="M12.354 1.646a.5.5 0 0 1 0 .708L6.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                                    </svg>
                                </button>
                                
                            </div>
                            <div class="col-lg-3 col-3 my-auto">
                                <button class="btn btn-sm" id="next-page" onclick="nextButton()">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-double-right" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M3.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L9.293 8 3.646 2.354a.5.5 0 0 1 0-.708z"/>
                                        <path fill-rule="evenodd" d="M7.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L13.293 8 7.646 2.354a.5.5 0 0 1 0-.708z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Kode Desa</th>
                                    <th>Kode Kecamatan</th>
                                    <th>Nama Desa</th>
                                </tr>
                            </thead>
                            <tbody id="village-data">
                                
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('script')
    <script>
        const token = `Bearer ${localStorage.getItem('token')}`;

        const countData = document.querySelector('#count-data');
        const prevBtn = document.querySelector('#prev-page');
        const nextBtn = document.querySelector('#next-page');

        let page = 1;
        let search = '';
        let url = '';

        async function prevButton(){
            page--;
            await showRegionalData();
        }

        async function nextButton(){
            page++;
            await showRegionalData();
        }

        async function getRegionalData(url){
            let res = await axios(url,  {
                headers:{
                    Authorization : token
                }
            })

            return res.data.data;
        }

        async function showRegionalData(){
            // try {
            let responses = await getRegionalData(`${url}?page=${page}&search=${search}`);

                console.log(responses.data.length);
                prevNextButton(responses);
                let list = '';

                responses.data.map(response => {
                    list += `
                    <tr>
                        <td>${response.kode}</td>
                        <td>${response.kode_kecamatan}</td>
                        <td>${response.nama}</td>
                    </tr>
                    `
                })
                document.querySelector('#village-data').innerHTML = list;
            // } catch (error) {
            //     console.log(error);
            // }
        }

        function prevNextButton(response){
            console.log(response);
            countData.innerHTML = `${response.data.length ? (page * response.per_page)-(response.per_page-1) : 0}-${response.data.length + (page * response.per_page)-response.per_page} dari ${response.total}`;

            if (page == 1) {
                prevBtn.classList.add('disabled');
                prevBtn.setAttribute('disabled','disabled');
            }else{
                prevBtn.classList.remove('disabled');
                prevBtn.removeAttribute('disabled');
            }

            if (page == response.last_page) {
                nextBtn.classList.add('disabled');
                nextBtn.setAttribute('disabled','disabled');
            }else{
                nextBtn.classList.remove('disabled');
                nextBtn.removeAttribute('disabled');
            }
        }

        async function showVillage(){
            page = 1;
            search = '';
            document.querySelector('#exampleModalLabel').innerHTML = 'Data Desa';

            url = `/api/village`;

            await showRegionalData();
        }
    </script>
@endsection
