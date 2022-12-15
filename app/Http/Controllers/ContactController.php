<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(){
        return view('admin.contact.index');
    }

    public function getApiData(){

        return response()->json([
            'status' => 'success',
            'data' =>Contact::where('name', 'like', '%' . request('search') . '%')->get(),
        ]); 
    }
}
