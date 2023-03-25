<?php

namespace App\Http\Controllers\Business;

use Carbon\Carbon;
use App\Models\Business;
use Illuminate\Http\Request;
use App\Models\SavingAccount;
use App\Helpers\BusinessUserHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SavingAccountController extends Controller
{
    public function noRefSavingAccountRecomendation(Business $business){
        $fix_ref_no = '';
        $date = date('Ymd');

        $endSavingAccount = SavingAccount::where('business_id', $business['id'])
                            ->where('no_ref', 'like', 'SA-'. $date . '%')
                            ->orderBy('id', 'desc')
                            ->first();

        $newAccountPayable = 'SA-' . $date . '0001';

        if ($endSavingAccount) {
            $split_end_invoice = explode('-', $endSavingAccount['no_ref']);

            $newNumber = (int)$split_end_invoice[1] + 1;

            $newAccountPayable = 'SA-' . $newNumber;
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
            'data' => SavingAccount::where(fn($query) => 
                                        $query->where('no_ref', 'like', '%' . request('search') . '%')
                                    )
                                    ->orWhereHas('contact', fn($query) => 
                                        $query->where('name', 'like', '%' . request('search') . '%')
                                    )
                                    ->with('contact')
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
        return view('business.saving-account.index', ['business' => $business, ]);
    }

    public function print(Business $business)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        $savingAccounts = SavingAccount::orderBy('id')->get();
        return view('business.saving-account.print', ['business' => $business, 'savingAccounts' => $savingAccounts,]);
    }

    public function store(Business $business, Request $request)
    {
        // validasi input revenue 
        $attributes = $request->validate([
            'no_ref' => 'required',
        ]);

        if (!$request->contact['id']){
            throw ValidationException::withMessages([
                'message' => [$message]
            ]);
        }

        $attributes['business_id'] = $business['id'];
        $attributes['contact_id'] = $request->contact['id'];
        
        $savingAccount = SavingAccount::create($attributes);

        return response()->json([
            'status' => 'success',
            'data' => $savingAccount->contact,
        ]);
    }

    public function show(Business $business, $id)
    {
        $savingAccount = SavingAccount::where('id', $id)
                                        ->with('contact')
                                        ->first();

        return response()->json([
            'status' => 'success',
            'data' => $savingAccount,
        ]);
    }

    public function update(Business $business, $id, Request $request)
    {
        // validasi input revenue 
        $attributes = $request->validate([
            'no_ref' => 'required',
        ]);

        if (!$request->contact['id']){
            throw ValidationException::withMessages([
                'message' => [$message]
            ]);
        }

        $attributes['business_id'] = $business['id'];
        $attributes['contact_id'] = $request->contact['id'];
        
        $savingAccount = SavingAccount::find($id);
        
        $savingAccount->update($attributes);

        return response()->json([
            'status' => 'success',
            'data' => $savingAccount->contact,
        ]);
    }
    

    public function destroy(Business $business, $id)
    {        
        $savingAccount = SavingAccount::find($id);
        
        $savingAccount->delete();

        return response()->json([
            'status' => 'success',
            'data' => $savingAccount->contact,
        ]);
    }

    public function book(Business $business, $id)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }

        $savingAccount = SavingAccount::find($id);
        return view('business.saving-account.book', ['business' => $business, 'savingAccount' => $savingAccount, 'id' => $id]);
    }

    public function getDataAccountPayable(Business $business, $id)
    {
        $savingAccountFilterByTime = SavingAccount::whereId($id)
                                        // ->whereHas('accountPayables')
                                        ->with('contact')
                                        ->with('accountPayables', fn($query) => 
                                            $query->filter(request(['date_from','date_to','this_week','this_month','this_year'])))
                                        ->first();

        $savingAccount = SavingAccount::whereId($id)
                                    // ->whereHas('accountPayables')
                                    ->withSum('accountPayables', 'debit')
                                    ->withSum('accountPayables', 'credit')
                                    ->first();

        $period = '';
        
        if (request('date_from') && request('date_to')) {
            $period =  request('date_from') == request('date_to') ? Carbon::parse(request('date_from'))->isoformat('MMM, D Y') : Carbon::parse(request('date_from'))->isoformat('MMM, D Y') . ' - ' . Carbon::parse(request('date_to'))->isoformat('MMM, D Y');
        } elseif (request('this_week')) {
            $period = Carbon::parse(now()->startOfWeek())->isoformat('MMM, D Y') . ' - ' . Carbon::parse(now()->endOfWeek())->isoformat('MMM, D Y');
            
        } elseif (request('this_month'))
        {
            $period = Carbon::now()->isoformat('MMMM, Y');
        } else{
            $period = Carbon::now()->isoformat('Y');
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'contact' => $savingAccountFilterByTime,
                'balance' => $savingAccountFilterByTime ?$savingAccountFilterByTime->accountPayables->sum('credit') - $savingAccountFilterByTime->accountPayables->sum('debit') : 0,
                'totalBalance' => $savingAccount ? (int)$savingAccount['account_payables_sum_credit'] - (int)$savingAccount['account_payables_sum_debit'] : 0,
                'period' => $period
            ]
        ]);
    }
}
