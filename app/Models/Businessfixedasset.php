<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Businessfixedasset extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'no_ref', 'name', 'value','salvage', 'is_active', 'useful_life', 'author', 'metod', 'business_id'];

    public function scopeFilter($query, array $filters)
    {
        // filter search
        $query->when($filters['search'] ?? false, function($query, $search){
                return $query->where('no_ref', 'like', '%' . $search . '%')
                ->orWhere('name', 'like', '%' . $search . '%')
                ->orWhere('date', 'like', '%' . $search . '%')
                ->orWhere('no_ref', 'like', '%' . $search . '%')
                ->orWhere('value', 'like', '%' . $search . '%')
                ->orWhere('salvage', 'like', '%' . $search . '%')
                ->orWhere('useful_life', 'like', '%' . $search . '%');
        });
    }
}
