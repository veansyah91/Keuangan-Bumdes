<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'alamat', 'no_hp', 'business_id'];

    public function invoice()
    {
        return $this->hasMany(Invoice::class);
    }

    public function accountReceivable()
    {
        return $this->hasMany(AccountReceivable::class);
    }
}
