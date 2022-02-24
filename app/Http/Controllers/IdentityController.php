<?php

namespace App\Http\Controllers;

use App\Models\Identity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use File;

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
            'nama_bumdes' => 'required',
            'desa' => 'required',
            'kecamatan' => 'required',
            'kabupaten' => 'required',
            'provinsi' => 'required',
            'alamat' => 'required',
            'kode_pos' => 'required',
            'hp' => 'required',
            'email' => 'required|email',
            'image' => 'image|file|max:2048',
            'image_bumdes' => 'image|file|max:2048',
        ]);

        if ($request->file('image')) {
            $validatedData['image'] = $request->file('image')->move('images/logo-provinsi/', $request->file('image')->getClientOriginalName());
        }

        if ($request->file('image_bumdes')) {
            $validatedData['image_bumdes'] = $request->file('image_bumdes')->move('images/logo-bumdes/', $request->file('image_bumdes')->getClientOriginalName());
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
            'logo_usaha' => $request->file('image_bumdes') ? $validatedData['image_bumdes'] : '',
            'nama_bumdes' => strtoupper($validatedData['nama_bumdes']),
            'email' => $validatedData['email'],
            'no_hp' => $validatedData['hp'],
            'kode_pos' => $validatedData['kode_pos'],
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
            'nama_bumdes' => 'required',
            'desa' => 'required',
            'kecamatan' => 'required',
            'kabupaten' => 'required',
            'provinsi' => 'required',
            'alamat' => 'required',
            'kode_pos' => 'required',
            'no_hp' => 'required',
            'email' => 'required|email',
            'image' => 'image|file|max:2048',
            'image_bumdes' => 'image|file|max:2048',
        ]);

        $validatedData['image'] = $identity['image'];
        $validatedData['image_bumdes'] = $identity['logo_usaha'];

        if ($request->file('image')) {
            File::delete($identity['image']);
            $validatedData['image'] = $request->file('image')->move('images/logo-provinsi/', $request->file('image')->getClientOriginalName());

        }

        if ($request->file('image_bumdes')) {
            File::delete($identity['logo_usaha']);
            $validatedData['image_bumdes'] = $request->file('image_bumdes')->move('images/logo-bumdes/', $request->file('image_bumdes')->getClientOriginalName());
        }

        $identity->update([
            'nama_provinsi' => $validatedData['provinsi'],
            'nama_kabupaten' => $validatedData['kabupaten'],
            'nama_kecamatan' => $validatedData['kecamatan'],
            'nama_desa' => strtoupper($validatedData['desa']),
            'alamat' => strtoupper($validatedData['alamat']),
            'kepala_desa' => strtoupper($validatedData['kepala_desa']),
            'ketua' => strtoupper($validatedData['ketua']),
            'image' => $validatedData['image'],
            'logo_usaha' => $validatedData['image_bumdes'],
            'nama_bumdes' => strtoupper($validatedData['nama_bumdes']),
            'email' => $validatedData['email'],
            'no_hp' => $validatedData['no_hp'],
            'kode_pos' => $validatedData['kode_pos'],
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
