<?php 

namespace App\Helpers;

use App\Models\Invoice;
use App\Models\ClosingIncomeActivity;
use App\Models\AccountReceivablePayment;

class BusinessIncomeHelper {
    public static function getCashier($date, $businessId)
    {
        return $income = Invoice::where('business_id', $businessId)->whereDate('created_at', $date)->get()->sum('jumlah');        
    }

    public static function getAccountReservePayment($date, $businessId)
    {
        return $payment = AccountReceivablePayment::whereHas('accountReceivable', function($query) use ($businessId){
                                                                $query->where('business_id', $businessId);
                                                            })
                                                            ->whereDate('created_at', $date)
                                                            ->get()
                                                            ->sum('jumlah_bayar');  
    }

    public static function getStatusClosing($date, $businessId)
    {
        return $closing = ClosingIncomeActivity::where('business_id', $businessId)->where('tanggal', $date)->first();
    }

}