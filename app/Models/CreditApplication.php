<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditApplication extends Model
{
    use HasFactory;

    use HasFactory;

    protected $fillable = [
        'value',
        'author',
        'no_ref',
        'contact_name',
        'contact_id',
        'date',
        'business_id',
        'product_id',
        'saving_account_id', 'status', 'due_date', 'tenor', 'other_cost', 'profit', 'unit_price', 'downpayment'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function savingAccount()
    {
        return $this->belongsTo(SavingAccount::class);
    }

    public function scopeStatus($query)
    {
        if (request('status')) {
            return $query->where('status', request('status'));
        }
    }

    public function scopeFilter($query, array $filters)
    {
        // filter search
        $query->when($filters['search'] ?? false, function($query, $search){
                return $query->where('no_ref', 'like', '%' . $search . '%')
                ->orWhere('contact_name', 'like', '%' . $search . '%')
                ->orWhere('value', 'like', '%' . $search . '%');
        });

        //filter by date between
        $query->when($filters['date_from'] ?? false, function ($query, $date_from) {
            return $query->where('date', '>=', $date_from);
        });

        $query->when($filters['date_to'] ?? false, function ($query, $date_to) {
            return $query->where('date', '<=', $date_to);
        });

        //filter by this week
        $query->when($filters['this_week'] ?? false, function ($query) {
            return $query->whereBetween('date', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ]);
        });

        // filter by this month
        $query->when($filters['this_month'] ?? false, function ($query) {
            return $query->whereBetween('date', [
                now()->startOfMonth(),
                now()->endOfMonth()
            ]);
        });

        // filter by this year
        $query->when($filters['this_year'] ?? false, function ($query) {
            return $query->whereBetween('date', [
                now()->startOfYear(),
                now()->endOfYear()
            ]);
        });
    }
}
