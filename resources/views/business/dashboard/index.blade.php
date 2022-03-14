@extends('layouts.app')

@section('navmenu')
    <x-navbar :kategori="$business->kategori" :id="$business->id"/>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10 col-12">
            <div class="card">
                <div class="card-header fs-4 fw-bold">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    
                    <div class="row justify-content-start fs-3 fw-bold">
                        <div class="col-md-4 col-12">
                            Kas
                        </div>
                        <div class="col-md-8 col-12">
                            : Rp. {{ $businessBalance ? number_format($businessBalance['sisa'],0,",",".") : 0 }}
                            <div class="btn-group">
                                <button type="button" class="btn btn-link btn-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('business.business-balance-activity.index', $business->id) }}" class="dropdown-item btn btn-outline-danger edit-expense">
                                            <i class="bi bi-list text-primary"></i>
                                            Aktivitas
                                        </a>
                                    </li>
                                    @role('ADMIN')
                                    <li>
                                        <button class="dropdown-item btn btn-outline-danger edit-expense" data-bs-toggle="modal" data-bs-target="#updateBalanceModal">
                                            <i class="bi bi-pencil-square text-success"></i>
                                            Ubah Saldo
                                        </button>
                                    </li>
                                    @endrole
                                </ul>
                            </div>
                        </div>
                    </div>

                    <hr>

                    @if ($business->kategori == 'Pulsa')
                        <div class="row justify-content-start fs-3 fw-bold mt-2">
                            <div class="col-md-4 col-12">
                                Saldo
                            </div>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-12 col-md-8 table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Server</th>
                                            <th>Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center">TDC</td>
                                            <td class="text-end">Rp. 3.000.000</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <hr> 
                    @endif

                    <div class="row justify-content-start fs-3 fw-bold mt-2">
                        <h4 class="fs-3 fw-bold">
                            Cash Flow
                        </h4>
                        <div>
                            <canvas id="myChart"></canvas>
                        </div>
                    </div>

                    <hr>

                    <div class="row justify-content-start fs-3 fw-bold mt-2">
                        <div class="col-md-4 col-12">
                            Asset
                        </div>
                        <div class="col-md-8 col-12">
                            : Rp. {{ number_format($sumAsset,0,",",".") }}
                        </div>
                    </div>

                    <hr>

                    @if ($business->kategori == 'Retail')
                        <div class="row justify-content-start fs-3 fw-bold mt-2">
                            <div class="col-md-4 col-12">
                                Nilai Stok
                            </div>
                            <div class="col-md-8 col-12">
                                : Rp. {{ number_format($total,0,",",".") }}
                            </div>
                        </div>
                    @endif

                    
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <form action="{{ route('business.dashboard.update-business-balance', [$business->id]) }}" method="post">
        @csrf   
        @method('patch')
        <div class="modal fade" id="updateBalanceModal" tabindex="-1" aria-labelledby="updateBalanceModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateBalanceModalLabel">Ubah Saldo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3 row">
                            <label for="input-balance" class="col-sm-2 col-form-label">Saldo</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="input-balance" name="input_balance" value="{{ $businessBalance ? $businessBalance['sisa'] : 0 }}" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Ubah</button>
                    </div>
                </div>
            </div>
        </div>
    </form>   
    
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        let pathUrl = window.location.pathname;
        let businessId = pathUrl[1]
    
        axios.get(`/api/${businessId}/dashboard/cashflow`)
        .then(res => {
            let months = res.data.data.label;
            let expenses = res.data.data.expenses;
            let incomes = res.data.data.incomes;
            let profits = res.data.data.profits;

            const labels = [...months];
        
            const data = {
                labels: labels,
                datasets: [{
                        label: 'Pemasukan',
                        backgroundColor: 'rgb(255, 99, 132)',
                        borderColor: 'rgb(255, 99, 132)',
                        data: [...incomes],
                    },
                    {
                        label: 'Pengeluaran',
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgb(54, 162, 235)',
                        data: [...expenses],
                    },
                    {
                        label: 'Laba',
                        backgroundColor: 'rgba(255, 205, 86, 0.2)',
                        borderColor: 'rgb(255, 205, 86)',
                        data: [...profits],
                    }
                
                ]
            };
        
            const config = {
                type: 'line',
                data: data,
                options: {}
            };

            const myChart = new Chart(
                document.getElementById('myChart'),
                config
            );
        })
        .catch(err => {
            console.log(err);
        })


    </script>

    <script>
        
    </script>

    <script type="text/javascript">
        
    </script>
@endsection
