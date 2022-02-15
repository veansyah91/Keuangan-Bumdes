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
                            Saldo
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
                                </ul>
                            </div>
                        </div>
                    </div>

                    <hr>

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
