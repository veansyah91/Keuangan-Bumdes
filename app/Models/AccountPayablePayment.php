<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountPayablePayment extends Model
{
    use HasFactory;
    
    protected $fillable = ['value', 'author', 'no_ref', 'business_id', 'contact_id', 'date', 'contact_name'];

    public function scopeFilter($query, array $filters)
    {
        // filter search
        $query->when($filters['search'] ?? false, function($query, $search){
            return $query->where('no_ref', 'like', '%' . $search . '%')
                        ->orWhere('contact_name', 'like', '%' . $search . '%');
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

        // filter by end year
        $query->when($filters['end_year'] ?? false, function ($query) {
            return $query->where('date','<=', now()->endOfYear());
        });
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
