<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Businessaccount extends Model
{
    use HasFactory;

    protected $fillable = ['name','code','is_cash','is_active','sub_category','sub_classification_account_id', 'business_id'];

    public function scopeFilter($query, array $filters)
    {
        // filter search
        $query->when($filters['search'] ?? false, function($query, $search){
            return $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('code', 'like', '%' . $search . '%')
                        ->orWhere('sub_category', 'like', '%' . $search . '%');
        });
    }

    public function scopeIsCash($query, array $filters)
    {

        $query->when($filters['is_cash'] ?? false, function($query, $is_cash){
            $query->where('is_cash', $is_cash);
            
        });
    }

    public function scopePayment($query, array $filters)
    {
        $query->when($filters['payment'] ?? false, function($query, $payment){
            $query->where('is_cash', $payment)->orWhere('sub_category', 'Piutang Usaha');
            
        });
    }

    public function scopePayable($query, array $filters)
    {
        $query->when($filters['payable'] ?? false, function($query, $payable){
            $query->where('is_cash', $payable)->orWhere('sub_category', 'Utang Usaha');
            
        });
    }
    
    public function cashflows()
    {
        return $this->hasMany(Businesscashflow::class, 'account_id');
    }

    public function ledgers()
    {
        return $this->hasMany(Businessledger::class, 'account_id');
    }
}
