<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function products()
    {
        return $this->belongsToMany(Product::class)->withPivot('jumlah', 'harga');
    }

    public function debt()
    {
        return $this->hasOne(Debt::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function accountReceivable()
    {
        return $this->hasOne(AccountReceivable::class);
    }
}
