<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavingAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'contact_id',
        'no_ref',
    ];


    public function scopeFilter($query, array $filters)
    {
        // filter search
        $query->when($filters['search'] ?? false, function($query, $search){
                return $query->where('no_ref', 'like', '%' . $search . '%');
        });
    }

    public function contact(){
        return $this->belongsTo(Contact::class);
    }

    public function accountPayables()
    {
        return $this->hasMany(AccountPayable::class);
    }
}
