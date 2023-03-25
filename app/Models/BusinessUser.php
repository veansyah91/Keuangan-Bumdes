<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessUser extends Model
{
    use HasFactory;

    protected $table = 'business_user';
    protected $guarded = [];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
