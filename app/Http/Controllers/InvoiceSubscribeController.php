<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Identity;
use App\Models\Subscribe;
use Illuminate\Http\Request;
use App\Models\SubscribeInvoice;
use Illuminate\Validation\Rules\Enum;
use App\Enums\InvoiceSubscribePackage;

class InvoiceSubscribeController extends Controller
{
    public function index(){

        $invoices = SubscribeInvoice::orderBy('id', 'desc')->paginate(50);
        return view('admin.invoice-subscribe.index', compact('invoices'));
    }

    public function create(){
        return view('admin.invoice-subscribe.create');
    }

    public function store(Request $request)
    {
        $attributes = $request->validate([
            'date' => 'required|date',
            'package' => 'required',[new Enum(InvoiceSubscribePackage::class)]
        ]);
        
        $attributes['no_ref'] = strval(rand(100000,1000000));
        $attributes['value'] = $attributes['package'] == 'monthly' ? '250000' : '2500000';

        $invoice = SubscribeInvoice::create($attributes);

        return redirect('/invoice-subscribe/' . $invoice['id']);
    }

    public function detail($id)
    {
        $invoice = SubscribeInvoice::find($id);

        $invoice['date_format'] = Carbon::createFromDate($invoice['date'])->toFormattedDateString(); 


        return view('admin.invoice-subscribe.detail', compact('invoice'));
    }
    
    public function paymentConfirmation($id)
    {
        $data = SubscribeInvoice::find($id);

        $data->update([
            'is_waiting' => true,
            'is_paid' => true
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function confirm(Request $request, $id)
    {

        $invoice = SubscribeInvoice::find($id);
        $subscribe = Subscribe::first();

        $duration = '+1month';

        if ($invoice['package'] == 'yearly') {
            $duration = '+1year';
        }

        $updateDate = date('Y-m-d', strtotime($duration, strtotime($subscribe['due_date'])));

        $invoice->update([
            'is_waiting' => false,
        ]);

        $subscribe->update([
            'due_date' => $updateDate
        ]);

        return redirect('/invoice-subscribe');
    }

    public function print($id)
    {
        $invoice = SubscribeInvoice::find($id);
        $invoice['date_format'] = Carbon::createFromDate($invoice['date'])->toFormattedDateString();

        $identity = Identity::first();
        $subscribe = Subscribe::first();
        $subscribe['date_format'] = Carbon::createFromDate($subscribe['due_date'])->toFormattedDateString();

        $today = Carbon::createFromDate(Date('Y-m-d'))->toFormattedDateString();

        return view('admin.invoice-subscribe.print', compact('invoice', 'identity', 'subscribe', 'today'));
    }
}
