<?php

namespace App\Http\Controllers;

use App\Models\Identity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class IdentityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.identity.index',[
            'identity' => Identity::first()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.identity.create');
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
            'kepala_desa' => 'required',
            'ketua' => 'required',
            'desa' => 'required',
            'kecamatan' => 'required',
            'kabupaten' => 'required',
            'provinsi' => 'required',
            'alamat' => 'required',
            'image' => 'image|file|max:2048',
        ]);

        if ($request->file('image')) {
            $validatedData['image'] = $request->file('image')->store('logo-desa');
        }

        Identity::create([
            'nama_provinsi' => $validatedData['provinsi'],
            'nama_kabupaten' => $validatedData['kabupaten'],
            'nama_kecamatan' => $validatedData['kecamatan'],
            'nama_desa' => strtoupper($validatedData['desa']),
            'alamat' => strtoupper($validatedData['alamat']),
            'kepala_desa' => strtoupper($validatedData['kepala_desa']),
            'ketua' => strtoupper($validatedData['ketua']),
            'image' => $request->file('image') ? $validatedData['image'] : '',
        ]);

        return redirect('/identity');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Identity  $identity
     * @return \Illuminate\Http\Response
     */
    public function show(Identity $identity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Identity  $identity
     * @return \Illuminate\Http\Response
     */
    public function edit(Identity $identity)
    {
        
        return view('admin.identity.edit', [
            'identity' => $identity
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Identity  $identity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Identity $identity)
    {
        $validatedData = $request->validate([
            'kepala_desa' => 'required',
            'ketua' => 'required',
            'desa' => 'required',
            'kecamatan' => 'required',
            'kabupaten' => 'required',
            'provinsi' => 'required',
            'alamat' => 'required',
            'image' => 'image|file|max:2048',
        ]);

        if ($request->file('image')) {
            Storage::delete($identity['image']);
            $validatedData['image'] = $request->file('image')->store('logo-desa');
        }

        $identity->update([
            'nama_provinsi' => $validatedData['provinsi'],
            'nama_kabupaten' => $validatedData['kabupaten'],
            'nama_kecamatan' => $validatedData['kecamatan'],
            'nama_desa' => strtoupper($validatedData['desa']),
            'alamat' => strtoupper($validatedData['alamat']),
            'kepala_desa' => strtoupper($validatedData['kepala_desa']),
            'ketua' => strtoupper($validatedData['ketua']),
            'image' => $request->file('image') ? $validatedData['image'] : '',
        ]);

        return redirect('/identity');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Identity  $identity
     * @return \Illuminate\Http\Response
     */
    public function destroy(Identity $identity)
    {
        
    }
}
