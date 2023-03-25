<?php

namespace App\Http\Controllers\Business;

use App\Models\Deposit;
use App\Models\Business;
use App\Models\Identity;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use App\Models\AccountPayable;
use App\Models\Businessledger;
use App\Models\Businessaccount;
use App\Models\Businessjournal;
use App\Models\Businesscashflow;
use App\Helpers\BusinessUserHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Riskihajar\Terbilang\Facades\Terbilang;

class WithdrawalController extends Controller
{
    public function noRefWithdrawalRecomendation(Business $business){
        $fix_ref_no = '';

        $date = str_replace('-', '', request('date'));

        $endDeposit = Withdrawal::where('business_id', $business['id'])
                            ->where('no_ref', 'like', 'W-'. $date . '%')
                            ->orderBy('id', 'desc')
                            ->first();

        $newAccountPayable = 'W-' . $date . '0001';

        if ($endDeposit) {
            $split_end_invoice = explode('-', $endDeposit['no_ref']);

            $newNumber = (int)$split_end_invoice[1] + 1;

            $newAccountPayable = 'W-' . $newNumber;
        }

        return response()->json([
            'status' => 'success',
            'data' => $newAccountPayable,
        ]);
    }

    public function getData(Business $business)
    {

        return response()->json([
            'status' => 'success',
            'data' => Withdrawal::filter(request(['date_from','date_to','this_week','this_month','this_year']))
                                ->where(fn($query) => 
                                        $query->where('no_ref', 'like', '%' . request('search') . '%')
                                    )
                                ->with('savingAccount')
                                ->latest()
                                ->paginate(50),
        ]);
    }

    public function index(Business $business)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        
        return view('business.withdrawal.index', [
            'business' => $business, ]);
    }

    public function store(Request $request, Business $business)
    {
        // validasi input revenue 
        $attributes = $request->validate([
            'no_ref' => 'required',
            'date' => 'required|date',
            'value' => 'required|numeric',
        ]); 

        if (!$request->contact['id']) {
            throw ValidationException::withMessages([
                'message' => [$message]
            ]);
        }

        $attributes['contact_name'] = $request->contact['name'];
        $attributes['saving_account_id'] = $request->contact['id'];
        $attributes['business_id'] = $business['id'];
        $attributes['author'] = $request->user()->name;

        $withdrawal = Withdrawal::create($attributes);
        //account for credit
        $account = Businessaccount::findOrFail($request->account['id']);
        $attributes['account_id'] = $account['id'];
        $attributes['account_name'] = $account['name'];
        $attributes['account_code'] = $account['code'];
        $attributes['credit'] = $attributes['value'];
        $attributes['debit'] = 0;

        $attributes['description'] = 'Penarikan Tunai Oleh ' . $attributes['contact_name'];
        $attributes['desc'] = $attributes['description'];
        $attributes['source'] = 'tarik tunai';

        Businessledger::create($attributes);

        $attributes['type'] = 'operation';

        //account for debit
        $account = Businessaccount::where('business_id', $business['id'])
                                    ->where('name', 'Tabungan Nasabah')
                                    ->first();
        $attributes['account_id'] = $account['id'];
        $attributes['account_name'] = $account['name'];
        $attributes['account_code'] = $account['code'];

        //to cashflow
        Businesscashflow::create($attributes);
        
        $attributes['debit'] = $attributes['value'];
        $attributes['credit'] = 0;
        Businessledger::create($attributes);

        //account payable
        AccountPayable::create($attributes);

        //business journal
        Businessjournal::create($attributes);
        return response()->json([
            'status' => 'success',
            'data' => $attributes
        ]);
    }

    public function update(Request $request, Business $business, $id)
    {
        // validasi input revenue 
        $attributes = $request->validate([
            'no_ref' => 'required',
            'date' => 'required|date',
            'value' => 'required|numeric',
        ]); 

        if (!$request->contact['id']) {
            throw ValidationException::withMessages([
                'message' => [$message]
            ]);
        }

        $withdrawal = Withdrawal::findOrFail($id);
        $no_ref_before = $withdrawal['no_ref'];

        $attributes['contact_name'] = $request->contact['name'];
        $attributes['saving_account_id'] = $request->contact['id'];
        $attributes['business_id'] = $business['id'];
        $attributes['author'] = $request->user()->name;

        $withdrawal->update($attributes);

        //account for credit
        $ledger = Businessledger::whereNoRef($no_ref_before)                        
                                ->whereBusinessId($business['id'])
                                ->where('debit', '>', 0)
                                ->first();

        $account = Businessaccount::findOrFail($request->account['id']);
        $attributes['account_id'] = $account['id'];
        $attributes['account_name'] = $account['name'];
        $attributes['account_code'] = $account['code'];
        $attributes['credit'] = $attributes['value'];
        $attributes['debit'] = 0;

        $attributes['description'] = 'Penarikan Tunai Oleh ' . $attributes['contact_name'];
        $attributes['desc'] = $attributes['description'];

        $ledger->update($attributes);

        //account for debit
        $ledger = Businessledger::whereNoRef($no_ref_before)                        
                                ->whereBusinessId($business['id'])
                                ->where('credit', '>', 0)
                                ->first();

        $account = Businessaccount::where('business_id', $business['id'])
                                    ->where('name', 'Tabungan Nasabah')
                                    ->first();
        $attributes['account_id'] = $account['id'];
        $attributes['account_name'] = $account['name'];
        $attributes['account_code'] = $account['code'];

        //to cashflow
        $attributes['type'] = 'operation';

        $cashflow = Businesscashflow::whereNoRef($no_ref_before)                        
                                    ->whereBusinessId($business['id'])
                                    ->first();

        $cashflow->update($attributes);
        $attributes['debit'] = $attributes['value'];
        $attributes['credit'] = 0;
        $ledger->update($attributes);

        //account payable
        $accountPayable = AccountPayable::whereNoRef($no_ref_before)                        
                        ->whereBusinessId($business['id'])
                        ->first();

        $accountPayable->update($attributes);

        //business journal
        $journal = Businessjournal::whereNoRef($no_ref_before)                        
                        ->whereBusinessId($business['id'])
                        ->first();

        $journal->update($attributes);

        return response()->json([
            'status' => 'success',
            'data' => $attributes
        ]);
    }

    public function show(Business $business, $id)
    {
        $withdrawal = Withdrawal::whereId($id)
                            ->with('savingAccount', fn($query) => 
                                $query->with('contact')
                            )
                            ->first();
       
        $withdrawal['terbilang'] = Terbilang::make($withdrawal['value']);
        $withdrawal['created_at_for_human'] = $withdrawal->updated_at->diffForHumans();
        $withdrawal['is_updated'] = $withdrawal->updated_at != $withdrawal->created_at ? true : false;

        //ledger dimana credit > 0
        $businessLedger = Businessledger::whereNoRef($withdrawal['no_ref'])                        
                                            ->whereBusinessId($business['id'])
                                            ->where('credit', '>', 0)
                                            ->first();

        $withdrawal['account'] = [
            'id' => $businessLedger['account_id'],
            'name' => $businessLedger['account_name'],
        ];

        return response()->json([
            'status' => 'success',
            'data' => $withdrawal
        ]);
    }

    public function destroy(Business $business, $id)
    {        
        $withdrawal = Withdrawal::find($id);

        //ledger
        $ledgers = Businessledger::whereBusinessId($business['id'])
                                ->whereNoRef($withdrawal['no_ref'])
                                ->get();

        if (count($ledgers) > 0) {
            foreach ($ledgers as $ledger) {
                $ledger->delete();
            }
        }

        //account payable
        $accountPayable = AccountPayable::whereBusinessId($business['id'])
                                    ->whereNoRef($withdrawal['no_ref'])
                                    ->first();
        
        $accountPayable->delete();

        //journal
        $journal = Businessjournal::whereBusinessId($business['id'])
                                ->whereNoRef($withdrawal['no_ref'])
                                ->first();

        $journal->delete();

        $withdrawal->delete();

        return response()->json([
            'status' => 'success',
            'data' => $withdrawal,
        ]);
    }

    public function printDetail(Business $business, $id){
        $identity = Identity::first();

        $withdrawal = Withdrawal::whereId($id)
                            ->with('savingAccount', fn($query) => 
                                $query->with('contact')
                            )
                            ->first();
       
        $withdrawal['terbilang'] = Terbilang::make($withdrawal['value']);
        $withdrawal['created_at_for_human'] = $withdrawal->updated_at->diffForHumans();
        $withdrawal['is_updated'] = $withdrawal->updated_at != $withdrawal->created_at ? true : false;
        
        return view('business.withdrawal.print-detail', compact('withdrawal', 'business', 'identity'));
    }
}
