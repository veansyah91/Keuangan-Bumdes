<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessBalanceActivity extends Model
{
    use HasFactory;

    protected $guarded;

    public function businessBalance()
    {
        return $this->belongsTo(BusinessBalance::class);
    }
}
