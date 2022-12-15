<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Ledger;
use App\Models\Account;
use App\Models\Journal;
use App\Models\Cashflow;
use Illuminate\Http\Request;
use App\Http\Resources\JournalResource;
use App\Http\Resources\JournalCollection;

class JournalController extends Controller
{
    
    public function index()
    {
        return view('admin.journal.index');
        
    }

    public function create()
    {
        return view('admin.journal.create');

    }

    public function store(Request $request)
    {
        $attributes = $request->validate([
            'no_ref' => 'required',
            'desc' => 'required',
            'value' => 'required|numeric',
            'date' => 'date',
            'detail' => 'string|nullable',
        ]);
        
        $attributes['author'] = $request->user()->name;

        Journal::create($attributes);

        $to_cashflow = false;
        $newRecords = [];

        $i = 0;
        foreach ($request->listInput as $list) {
            $account = Account::find($list['accountId']);

            Ledger::create([
                'account_name' => $list['accountName'],
                'account_id' => $list['accountId'],
                'debit' => $list['debit'],
                'credit' => $list['credit'],
                'no_ref' => $attributes['no_ref'],
                'date' => $attributes['date'],
                'description' => $attributes['desc'],
                'account_code' => $account['code'],
                'author' =>$request->user()->name
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
                ];

                $i++;
            } 
        }

        if ($to_cashflow) {
            foreach ($newRecords as $newRecord) {
                Cashflow::create( $newRecord);
            }
        }

        return response()->json([
            'status' => 'success',
            'data' =>  $attributes,
        ]);
    }

    public function show($id)
    {
        $data = Journal::find($id);
        $data['created_at_for_human'] = $data->updated_at->diffForHumans();
        $data['is_updated'] = $data->updated_at != $data->created_at ? true : false;
        $data['ledgers'] = Ledger::where('no_ref', $data->no_ref)->get();


        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }

    public function edit($id)
    {
        return view('admin.journal.edit', [
            'journal_id' => $id
        ]);

    }

    public function update(Request $request, $id)
    {
        $attributes = $request->validate([
            'no_ref' => 'required',
            'desc' => 'required',
            'value' => 'required|numeric',
            'date' => 'date',
            'detail' => 'string|nullable',
        ]);
        $attributes['author'] = $request->user()->name;

        $journal = Journal::find($id);

        // delete data in ledgers table first
        $ledgers = Ledger::where('no_ref', $journal->no_ref)->get();

        foreach ($ledgers as $ledger) {
            $ledger->delete();
        }

        //delete data in cashflows table if exist
        $cashflows = Cashflow::where('no_ref', $journal->no_ref)->get();
        if (count($cashflows) > 0) {
            foreach ($cashflows as $cashflow) {
                $cashflow->delete();
            }
        }

        $journal->update($attributes);

        //create new ledger
        $to_cashflow = false;
        $newRecords = [];

        $i = 0;
        foreach ($request->listInput as $list) {
            $account = Account::find($list['accountId']);

            Ledger::create([
                'account_name' => $list['accountName'],
                'account_id' => $list['accountId'],
                'debit' => $list['debit'],
                'credit' => $list['credit'],
                'no_ref' => $attributes['no_ref'],
                'date' => $attributes['date'],
                'description' => $attributes['desc'],
                'account_code' => $account['code'],
                'author' =>$request->user()->name
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
                ];

                $i++;
            }

            if ($to_cashflow) {
                foreach ($newRecords as $newRecord) {
                    Cashflow::create( $newRecord);
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'data' => $journal,
        ]);
    }

    public function destroy($id)
    {
        $journal = Journal::find($id);

        // delete data in ledgers table first
        $ledgers = Ledger::where('no_ref', $journal->no_ref)->get();

        foreach ($ledgers as $ledger) {
            $ledger->delete();
        }

        $cashflows = Cashflow::where('no_ref', $journal->no_ref)->get();
        if (count($cashflows) > 0) {
            foreach ($cashflows as $cashflow) {
                $cashflow->delete();
            }
        }

        $journal->delete();

        return response()->json([
            'status' => 'success',
            'data' => $journal,
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

    public function noRefJournalRecomendation(){
        $ref_no = explode("-", request('search'));
        $journal = Journal::filter(request(['search']))->orderBy('id', 'desc')->first();

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

    public function getApiData(){
        return response()->json([
            'status' => 'success',
            'data' => Journal::filter(request(['search','date_from','date_to','this_week','this_month','this_year']))->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(50)->withQueryString()
        ]);
    }

    public function print(){
        $journals = new JournalCollection(Journal::filter(request(['search','date_from','date_to','this_week','this_month','this_year']))->orderBy('date', 'asc')->get());
       
        return view('admin.journal.print', [
            'journals' => $journals,
            'author' => request()->user()
        ]);
    }

    public function printDetail($id){
        $journal = Journal::find($id);
        $journal['date_format'] = Carbon::createFromDate($journal->date)->toFormattedDateString();

        $ledgers = Ledger::where('no_ref', $journal->no_ref)->get();

        return view('admin.journal.print-detail', compact('journal', 'ledgers'));
    }
}
