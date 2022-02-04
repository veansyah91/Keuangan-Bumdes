<?php

namespace App\Http\Controllers;

use App\Models\Outcome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class OutcomeController extends Controller
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

        $outcomes = ($tanggal_awal && $tanggal_akhir) ? 
                    Outcome::whereBetween('tanggal_keluar', [$tanggal_awal, $tanggal_akhir])->orderBy('tanggal_keluar', 'desc')->paginate(10)->withQueryString()
                    : Outcome::orderBy('tanggal_keluar', 'desc')->paginate(10);

        return view('admin.outcome.index', [
            'outcomes' => $outcomes,
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
            'tanggal_keluar' => 'date',
            'image' => 'image|file|max:2048',
        ]);

        if ($request->file('image')) {
            $validatedData['image'] = $request->file('image')->store('outcome');
        }

        $user = Auth::user();

        Outcome::create([
            'jumlah' => $validatedData['jumlah'],
            'keterangan' => $validatedData['keterangan'],
            'tanggal_keluar' => $validatedData['tanggal_keluar'],
            'operator' => $user['name'],
            'image' => $request->file('image') ? $validatedData['image'] : '',
        ]);

        return redirect('/outcome');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Outcome  $outcome
     * @return \Illuminate\Http\Response
     */
    public function show(Outcome $outcome)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Outcome  $outcome
     * @return \Illuminate\Http\Response
     */
    public function edit(Outcome $outcome)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Outcome  $outcome
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Outcome $outcome)
    {

        $validatedData = $request->validate([
            'jumlah' => 'required|numeric',
            'keterangan' => 'required',
            'tanggal_keluar' => 'date',
            'image' => 'image|file|max:2048',
        ]);

        if ($request->file('image')) {
            Storage::delete($outcome['image']);
            $validatedData['image'] = $request->file('image')->store('outcome');
        }

        $outcome->update([
            'jumlah' => $validatedData['jumlah'],
            'keterangan' => $validatedData['keterangan'],
            'tanggal_keluar' => $validatedData['tanggal_keluar'],
            'image' => $request->file('image') ? $validatedData['image'] : '',
        ]);

        return redirect('/outcome');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Outcome  $outcome
     * @return \Illuminate\Http\Response
     */
    public function destroy(Outcome $outcome)
    {
        if ($outcome['image']) {
            Storage::delete($outcome['image']);
        }

        $outcome->delete();

        return redirect('/outcome');
    }
}
