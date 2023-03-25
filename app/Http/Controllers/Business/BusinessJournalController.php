<?php

namespace App\Http\Controllers\Business;

use Carbon\Carbon;
use App\Models\Business;
use App\Models\Identity;
use Illuminate\Http\Request;
use App\Models\Businessledger;
use App\Models\Businessaccount;
use App\Models\Businessjournal;
use App\Models\Businesscashflow;
use App\Helpers\BusinessUserHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BusinessJournalController extends Controller
{
    public function getApiData(Business $business){
        return response()->json([
            'status' => 'success',
            'data' => Businessjournal::filter(request(['search','date_from','date_to','this_week','this_month','this_year']))->where('business_id', $business['id'])->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(50)->withQueryString()
        ]);
    }

    public function newNoRefJournal($no_ref_request, $no_ref_journal){
        $split_journal_ref_no = explode("-", $no_ref_journal);
        $old_ref_no = (int)$split_journal_ref_no[1];
        $new_ref_no = 1000000 + $old_ref_no + 1;
        $new_ref_no_string = strval($new_ref_no);
        $new_ref_no_string_without_first_digit = substr($new_ref_no_string, 1);
        return $fix_ref_no = $no_ref_request . '-' . $new_ref_no_string_without_first_digit;
    }

    public function noRefJournalRecomendation(Business $business){
        $ref_no = explode("-", request('search'));
        $journal = Businessjournal::filter(request(['search']))->where('business_id', $business['id'])->orderBy('id', 'desc')->first();

        $fix_ref_no = '';

        if($journal){
            $fix_ref_no = $this->newNoRefJournal($ref_no[0], $journal->no_ref);
        }else{
            $fix_ref_no = $ref_no[0] . '-000001';
        }

        return response()->json([
            'status' => 'success',
            'data' => $fix_ref_no,
        ]);
    }

    public function index(Business $business)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        return view('business.journal.index', compact('business'));
    }

    public function create(Business $business)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        return view('business.journal.create', compact('business'));
    }

    public function store(Request $request, Business $business)
    {
        $attributes = $request->validate([
            'no_ref' => 'required',
            'desc' => 'required',
            'value' => 'required|numeric',
            'date' => 'date',
            'detail' => 'string|nullable',
        ]);
        
        $attributes['author'] = $request->user()->name;
        $attributes['business_id'] = $business['id'];

        Businessjournal::create($attributes);

        $to_cashflow = false;
        $newRecords = [];

        $i = 0;
        foreach ($request->listInput as $list) {
            $account = Businessaccount::find($list['accountId']);

            Businessledger::create([
                'account_name' => $list['accountName'],
                'account_id' => $list['accountId'],
                'debit' => $list['debit'],
                'credit' => $list['credit'],
                'no_ref' => $attributes['no_ref'],
                'date' => $attributes['date'],
                'description' => $attributes['desc'],
                'account_code' => $account['code'],
                'author' =>$request->user()->name,
                'business_id' => $business['id']
            ]);

            //cash flow

            if ($account['is_cash']) {
                $to_cashflow = true;
            } else {
                // create new array data to cashflows table
                $type = 'operation';

                $temp =  substr($account['code'], 0 , 2);
                
                if ((int)$temp > 15 && (int)$temp < 20) {
                    $type = 'investment';
                }

                if ((int)$temp > 29 && (int)$temp < 40) {
                    $type = 'finance';
                }

                $newRecords[$i] = [
                    'account_id' => $account['id'],
                    'account_code' => $account['code'],
                    'account_name' => $account['name'],
                    'no_ref' => $attributes['no_ref'],
                    'type' => $type,
                    'date' => $attributes['date'],
                    'credit' => $list['debit'] > 0 ? $list['debit'] : 0,
                    'debit' => $list['credit'] > 0 ? $list['credit'] : 0,
                    'business_id' => $business['id']
                ];

                $i++;
            } 
        }

        if ($to_cashflow) {
            foreach ($newRecords as $newRecord) {
                Businesscashflow::create( $newRecord);
            }
        }

        return response()->json([
            'status' => 'success',
            'data' =>  $attributes,
        ]);
    }

    public function show(Business $business, Businessjournal $businessjournal)
    {
        // $data = Journal::find($id);
        $businessjournal['created_at_for_human'] = $businessjournal->updated_at->diffForHumans();
        $businessjournal['is_updated'] = $businessjournal->updated_at != $businessjournal->created_at ? true : false;
        $businessjournal['ledgers'] = Businessledger::where('business_id', $business['id'])
                                                    ->where('no_ref', $businessjournal->no_ref)
                                                    ->orderBy('credit')
                                                    ->orderBy('account_code')
                                                    ->get();

        return response()->json([
            'status' => 'success',
            'data' => $businessjournal,
        ]);
    }

    public function edit(Business $business, Businessjournal $businessjournal)
    {
        return view('business.journal.edit', [
            'journal' => $businessjournal,
            'business' => $business
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Businessjournal  $businessjournal
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Business $business, Businessjournal $businessjournal)
    {
        $attributes = $request->validate([
            'no_ref' => 'required',
            'desc' => 'required',
            'value' => 'required|numeric',
            'date' => 'date',
            'detail' => 'string|nullable',
        ]);
        $attributes['author'] = $request->user()->name;


        // delete data in ledgers table first
        $ledgers = Businessledger::where('no_ref', $businessjournal->no_ref)->get();

        foreach ($ledgers as $ledger) {
            $ledger->delete();
        }

        //delete data in cashflows table if exist
        $cashflows = Businesscashflow::where('no_ref', $businessjournal->no_ref)->get();
        if (count($cashflows) > 0) {
            foreach ($cashflows as $cashflow) {
                $cashflow->delete();
            }
        }

        $businessjournal->update($attributes);

        //create new ledger
        $to_cashflow = false;
        $newRecords = [];

        $i = 0;
        foreach ($request->listInput as $list) {
            $account = Businessaccount::find($list['accountId']);

            Businessledger::create([
                'account_name' => $list['accountName'],
                'account_id' => $list['accountId'],
                'debit' => $list['debit'],
                'credit' => $list['credit'],
                'no_ref' => $attributes['no_ref'],
                'date' => $attributes['date'],
                'description' => $attributes['desc'],
                'account_code' => $account['code'],
                'author' =>$request->user()->name,
                'business_id' => $business['id']
            ]);

            //cash flow

            if ($account['is_cash']) {
                $to_cashflow = true;
            } else {
                // create new array data to cashflows table
                $type = 'operation';

                $temp =  substr($account['code'], 0 , 2);
                
                if ((int)$temp > 15 && (int)$temp < 20) {
                    $type = 'investment';
                }

                if ((int)$temp > 29 && (int)$temp < 40) {
                    $type = 'finance';
                }

                $newRecords[$i] = [
                    'account_id' => $account['id'],
                    'account_code' => $account['code'],
                    'account_name' => $account['name'],
                    'no_ref' => $attributes['no_ref'],
                    'type' => $type,
                    'date' => $attributes['date'],
                    'credit' => $list['debit'] > 0 ? $list['debit'] : 0,
                    'debit' => $list['credit'] > 0 ? $list['credit'] : 0,
                    'business_id' => $business['id']
                ];

                $i++;
            }

            if ($to_cashflow) {
                foreach ($newRecords as $newRecord) {
                    Businesscashflow::create( $newRecord);
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $businessjournal,
        ]);
    }

    public function destroy( Business $business, Businessjournal $businessjournal)
    {

        // delete data in ledgers table first
        $ledgers = Businessledger::where('no_ref', $businessjournal->no_ref)->get();

        if (count($ledgers) > 0) {
            foreach ($ledgers as $ledger) {
                $ledger->delete();
            }
        }

        $cashflows = Businesscashflow::where('no_ref', $businessjournal->no_ref)->get();
        if (count($cashflows) > 0) {
            foreach ($cashflows as $cashflow) {
                $cashflow->delete();
            }
        }

        $businessjournal->delete();

        return response()->json([
            'status' => 'success',
            'data' => $businessjournal,
        ]);
    }

    public function print(Business $business){
        $identity = Identity::first();

        $journals = Businessjournal::where('business_id', $business['id'])->filter(request(['search','date_from','date_to','this_week','this_month','this_year']))->orderBy('date', 'asc')->get();

        $period = '';
        if (request('date_from') && request('date_to')) {
            $period = request('date_from') == request('date_to') ? Carbon::parse(request('date_from'))->isoformat('MMM, D Y') : Carbon::parse(request('date_from'))->isoformat('MMM, D Y') . ' - ' . Carbon::parse(request('date_to'))->isoformat('MMM, D Y');
        } elseif (request('this_week')) {
            $period = Carbon::parse(now()->startOfWeek())->isoformat('MMM, D Y') . ' - ' . Carbon::parse(now()->endOfWeek())->isoformat('MMM, D Y');
            
        } elseif (request('this_month'))
        {
            $period = Carbon::now()->isoformat('MMMM, Y');
        } else{
            $period = Carbon::now()->isoformat('Y');
        }
       
        return view('business.journal.print', [
            'journals' => $journals,
            'business' => $business,
            'period' => $period,
            'author' => request()->user()
        ]);
    }

    public function printDetail(Business $business, Businessjournal $businessjournal){
        $businessjournal['date_format'] = Carbon::createFromDate($businessjournal->date)->toFormattedDateString();

        $ledgers = Businessledger::where('business_id', $business['id'])->where('no_ref', $businessjournal->no_ref)->orderBy('account_code')->orderBy('credit')->get();

        return view('business.journal.print-detail', compact('businessjournal', 'ledgers', 'business'));
    }
}
