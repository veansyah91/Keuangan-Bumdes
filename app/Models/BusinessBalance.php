<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessBalance extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function businessBalanceActivity()
    {
        return $this->hasMany(BusinessBalanceActivity::class);
    }
}
