<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_ref', 
        'name', 
        'email', 
        'type', 
        'phone', 
        'address',
    ];

    protected $with = ['savingAccount', 'accountReceivables', 'accountPayables', 'invoices', 'purchaseGoods', 'detail'];

    public function scopeFilter($query, array $filters)
    {
        // filter search
        $query->when($filters['search'] ?? false, function($query, $search){
                return $query->where('no_ref', 'like', '%' . $search . '%')
                ->orWhere('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%')
                ->orWhere('type', 'like', '%' . $search . '%');
        });
    }
    public function scopeType($query, array $filters)
    {
        // filter type
        $query->when($filters['type'] ?? false, function($query, $type){
            return $query->where('type', $type);
        });
    }

    public function accountReceivables()
    {
        return $this->hasMany(AccountReceivable::class);
    }

    public function accountPayables()
    {
        return $this->hasMany(AccountPayable::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function purchaseGoods()
    {
        return $this->hasMany(PurchaseGoods::class);
    }

    public function detail(){
        return $this->hasOne(ContactDetail::class);
    }

    public function savingAccount(){
        return $this->hasMany(SavingAccount::class);
    }
}
