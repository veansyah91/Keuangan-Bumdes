<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'value',
        'author',
        'no_ref',
        'contact_name',
        'date',
        'business_id',
        'saving_account_id'
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function savingAccount()
    {
        return $this->belongsTo(SavingAccount::class);
    }

    public function scopeFilter($query, array $filters)
    {

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
