<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Businesscashflow extends Model
{
    use HasFactory;

    protected $fillable = ['account_id','no_ref','account_code','account_name','type','date', 'debit', 'credit', 'business_id'];
}
