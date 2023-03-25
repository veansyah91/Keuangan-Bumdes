<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'nkk',
        'nik',
        'province',
        'district',
        'village',
        'regency',
        'province',
        'code_pos',
        'address_detail',
    ];

    public function scopeFilter($query, array $filters)
    {
        // filter search
        $query->when($filters['search'] ?? false, function($query, $search){
                return $query->where('nik', 'like', '%' . $search . '%')
                ->orWhere('nkk', 'like', '%' . $search . '%')
                ->orWhere('province', 'like', '%' . $search . '%')
                ->orWhere('regency', 'like', '%' . $search . '%')
                ->orWhere('district', 'like', '%' . $search . '%')
                ->orWhere('village', 'like', '%' . $search . '%')
                ->orWhere('address_detail', 'like', '%' . $search . '%');
        });
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
