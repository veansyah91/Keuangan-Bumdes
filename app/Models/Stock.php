<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = ['qty', 'unit', 'product_id', 'debit', 'credit', 'no_ref', 'contact', 'date', 'business_id', 'account_id', 'account_name'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function incomingItem()
    {
        return $this->belongsTo(Incomingitem::class);
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

        //filter by end week
        $query->when($filters['end_week'] ?? false, function ($query) {
            return $query->where('date', '<=', now()->endOfWeek());
        });

        //filter by select month
        $query->when($filters['month'] ?? false, function ($query, $month) {
            return $query->whereMonth('date',  $month);
        });

        //filter until select month
        $query->when($filters['month_selected'] ?? false, function ($query, $month_selected) {
            return $query->whereMonth('date', '<=',  $month_selected);
        });

        // filter by this month
        $query->when($filters['this_month'] ?? false, function ($query) {
            return $query->whereBetween('date', [
                now()->startOfMonth(),
                now()->endOfMonth()
            ]);
        });

        // filter by end month
        $query->when($filters['end_month'] ?? false, function ($query) {
            return $query->where('date', '<=', now()->endOfMonth());
        });

        // filter by this year
        $query->when($filters['this_year'] ?? false, function ($query) {
            return $query->whereBetween('date', [
                now()->startOfYear(),
                now()->endOfYear()
            ]);
        });

        //filter by select year
        $query->when($filters['year'] ?? false, function ($query, $year) {
            return $query->whereYear('date', $year);
        });

        //filter until select year
        // $query->when($filters['year_selected'] ?? false, function ($query, $year_selected) {
        //     return $query->whereYear('date','<=', $year_selected);
        // });

        // filter by end year
        $query->when($filters['end_year'] ?? false, function ($query) {
            return $query->where('date','<=', now()->endOfYear());
        });
    }
}
