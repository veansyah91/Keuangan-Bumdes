<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubClassificationAccount extends Model
{
    use HasFactory;

    protected $fillable = ['name','code'];

    public function scopeFilter($query, array $filters)
    {
        // filter search
        $query->when($filters['search'] ?? false, function($query, $search){
            return $query->where('name', 'like', '%' . $search . '%');
        });
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }
}
