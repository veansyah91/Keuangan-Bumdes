<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscribeInvoice extends Model
{
    use HasFactory;

    protected $fillable = ['no_ref', 'value', 'package', 'is_paid', 'date', 'is_waiting'];
}
