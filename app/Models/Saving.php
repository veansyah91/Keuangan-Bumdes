<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saving extends Model
{
    use HasFactory;

    protected $fillable = [
        'debit',
        'credit',
        'author',
        'no_ref',
        'contact_name',
        'date',
        'description',
        'business_id',
        'contact_id',
        'saving_account_id',
    ];

    public function savingAccount()
    {
        return $this->belongsTo(SavingAccount::class);
    }
}
