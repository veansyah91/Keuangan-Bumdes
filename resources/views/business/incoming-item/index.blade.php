@extends('layouts.app')

@section('navmenu')
    <x-navbar :kategori="$business->kategori" :id="$business->id"/>
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header fs-4 fw-bold">{{ __('Barang Masuk') }}</div>

                <div class="card-body">
                    <a class="btn btn-primary" href="{{ route('business.incoming-item.create', $business->id) }}">Tambah Data</a>


                    <div class="row justify-content-center">
                        <div class="col-12">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nomor Nota</th>
                                        <th>Tanggal Nota</th>
                                        <th>Tanggal Masuk</th>
                                        <th>Total</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($incomingItems->isNotEmpty())
                                        @foreach ($incomingItems as $incomingItem)
                                            <tr>
                                                <td>{{ $incomingItem->nomor_nota }}</td>
                                                <td>{{ $incomingItem->tanggal_nota }}</td>
                                                <td>{{ $incomingItem->tanggal_masuk }}</td>
                                                <td>Rp. {{ number_format($incomingItem->jumlah,0,",",".") }}</td>
                                                <td>
                                                    <a class="btn btn-sm btn-success" href="{{ url('/' . $business->id . '/incoming-item/create?incomingItemId=' . $incomingItem->id) }}">perbarui</a>
                                                </td>
                                            </tr>
                                        @endforeach                                    
                                    @else
                                        <tr>
                                            <td class="text-center">
                                                <i>Tidak Ada Data</i>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>

                            <div class="d-flex justify-content-end x-overflow-auto">
                                {{ $incomingItems->links() }}
                            </div>
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
    </script>
@endsection
