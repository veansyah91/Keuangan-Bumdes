<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Business;
use App\Models\Subscribe;
use App\Models\BusinessUser;
use Illuminate\Http\Request;
use App\Helpers\BusinessUserHelper;
use Illuminate\Support\Facades\Auth;

class OverDueController extends Controller
{
    public function index()
    {
        $subscribe = Subscribe::first();
        $subscribe['date_format'] = Carbon::createFromDate($subscribe['due_date'])->toFormattedDateString(); 
        
        if (Auth::user()->hasRole('ADMIN|DEV')) {
            return view('overdue.admin', [
                'subscribe' => $subscribe
            ]);
        }

        $businessUser = BusinessUser::whereUserId(Auth::user()['id'])->first();

        return redirect()->route('over.due.business', [
            'business' => $businessUser['business_id']
        ]);
    }

    public function business(Business $business)
    {
        $businessUser = BusinessUserHelper::index($business['id'], Auth::user()['id']);

        $subscribe = Subscribe::first();
        $subscribe['date_format'] = Carbon::createFromDate($subscribe['due_date'])->toFormattedDateString(); 
        
        if (!$businessUser && !Auth::user()->hasRole('ADMIN')) {
            return abort(403);
        }
        return view('overdue.index', [
            'business' => $business,
            'subscribe' => $subscribe
        ]);
    }
}
