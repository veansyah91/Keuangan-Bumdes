<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountReceivable extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function accountReceivablePayment()
    {
        return $this->hasMany(AccountReceivablePayment::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
