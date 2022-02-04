<?php

namespace App\Http\Controllers;

use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncomeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $tanggal_awal = $request['tanggal_awal'];
        $tanggal_akhir = $request['tanggal_akhir'];

        $incomes = ($tanggal_awal && $tanggal_akhir) ? 
                    Income::whereBetween('tanggal_masuk', [$tanggal_awal, $tanggal_akhir])->orderBy('tanggal_masuk', 'desc')->paginate(10)->withQueryString()
                    : Income::orderBy('tanggal_masuk', 'desc')->paginate(10);

        return view('admin.income.index', [
            'incomes' => $incomes,
            'tanggal_awal' => $tanggal_awal,
            'tanggal_akhir' => $tanggal_akhir,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'jumlah' => 'required|numeric',
            'keterangan' => 'required',
            'tanggal_masuk' => 'date',
        ]);

        $user = Auth::user();

        Income::create([
            'jumlah' => $validatedData['jumlah'],
            'keterangan' => $validatedData['keterangan'],
            'tanggal_masuk' => $validatedData['tanggal_masuk'],
            'operator' => $user['name']
        ]);

        return redirect('/income');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Income  $income
     * @return \Illuminate\Http\Response
     */
    public function show(Income $income)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Income  $income
     * @return \Illuminate\Http\Response
     */
    public function edit(Income $income)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Income  $income
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Income $income)
    {
        $validatedData = $request->validate([
            'jumlah' => 'required|numeric',
            'keterangan' => 'required',
            'tanggal_masuk' => 'date',
        ]);

        $income->update([
            'jumlah' => $validatedData['jumlah'],
            'keterangan' => $validatedData['keterangan'],
            'tanggal_masuk' => $validatedData['tanggal_masuk'],
        ]);

        return redirect('/income');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Income  $income
     * @return \Illuminate\Http\Response
     */
    public function destroy(Income $income)
    {
        $income->delete();

        return redirect('/income');
    }
}
