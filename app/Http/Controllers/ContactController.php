<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Expense;
use App\Models\Revenue;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ContactController extends Controller
{
    public function newNoRefContact($no_ref_request, $no_ref_contact){
        $split_contact_ref_no = explode("-", $no_ref_contact);
        $old_ref_no = (int)$split_contact_ref_no[1];
        $new_ref_no = 1000 + $old_ref_no + 1;
        $new_ref_no_string = strval($new_ref_no);
        $new_ref_no_string_without_first_digit = substr($new_ref_no_string, 1);
        return $fix_ref_no = $no_ref_request . '-' . $new_ref_no_string_without_first_digit;
    }

    public function noRefContactRecomendation()
    {
        $fix_ref_no = '';

        $ref = 'SUPP';

        if (request('type') == 'Customer') {
            $ref = 'CUST';
        } 

        $contact = Contact::where('no_ref', 'like', $ref . '%')->orderBy('id', 'desc')->first();

        if($contact){
            $fix_ref_no = $this->newNoRefContact($ref, $contact->no_ref);
        }else{
            $fix_ref_no = $ref . '-001';
        }

        return response()->json([
            'status' => 'success',
            'data' => $fix_ref_no,
        ]);
    }

    public function index(){
        return view('admin.contact.index');
    }

    public function store(Request $request)
    {
        // validasi input revenue 
        $attributes = $request->validate([
            'no_ref' => 'required',
            'name' => 'required',
            'email' => 'email:rfc|nullable',
            'type' => 'required',
            'phone' => 'string|nullable',
            'address' => 'string|nullable',
        ]);

        $contact = Contact::create($attributes);

        return response()->json([
            'status' => 'success',
            'data' => $attributes,
        ]); 
    }

    public function show(Contact $contact)
    {
        return response()->json([
            'status' => 'success',
            'data' => $contact,
        ]); 
    }

    public function update(Request $request, Contact $contact)
    {
         // validasi input revenue 
         $attributes = $request->validate([
            'no_ref' => 'required',
            'name' => 'required',
            'email' => 'email:rfc|nullable',
            'type' => 'required',
            'phone' => 'string|nullable',
            'address' => 'string|nullable',
        ]);

        $contact->update($attributes);

        return response()->json([
            'status' => 'success',
            'data' => $contact,
        ]); 
    }

    public function destroy(Contact $contact)
    {
        // cek apakah telah digunakan di pendapatan dan pengeluaran
        $contact_revenue = Revenue::where('contact', $contact['name'])->first();
        $contact_expense = Expense::where('contact', $contact['name'])->first();

        if ($contact_revenue || $contact_expense) {
            $message = "Tidak Bisa Dihapus, Kontak " . $contact['name'] . " Telah Digunakan Pada Transaksi";
            throw ValidationException::withMessages([
                'message' => [$message]
            ]);
        }

        $contact->delete();
        return response()->json([
            'status' => 'success',
            'data' => $contact,
        ]); 
    }

    public function getData(){

        return response()->json([
            'status' => 'success',
            'data' =>Contact::filter(request(['search']))->where('type','!=', 'Business')->orderBy('id', 'desc')->paginate(50),
        ]); 
    }

    public function getApiData(){

        return response()->json([
            'status' => 'success',
                'data' =>Contact::filter(request(['search']))->type(request(['type']))->get(),
        ]); 
    }
}
