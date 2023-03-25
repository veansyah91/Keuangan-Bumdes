<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccountReceivable extends Model
{
    use HasFactory;

    protected $fillable = ['no_ref', 'business_id', 'invoice_id', 'contact_id', 'contact_name', 'debit','credit', 'date', 'description', 'late', 'due_date', 'tenor', 'debt_submission_id', 'category', 'author', 'status', 'is_paid_off', 'credit_application_id', 'due_date_temp'];
     
    protected $with = ['invoice'];

    public function scopeFilter($query, array $filters)
    {
        // filter search
        $query->when($filters['search'] ?? false, function($query, $search){
            return $query->where('no_ref', 'like', '%' . $search . '%')
                        ->orWhere('date', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%')
                        ->orWhere('contact_name', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%');
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

    public function overDue()
    {
        return $this->hasOne(OverDue::class);
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function debtSubmission()
    {
        return $this->belongsTo(DebtSubmission::class);
    }

    public function creditApplication()
    {
        return $this->belongsTo(CreditApplication::class);
    }

    public function accountReceivablePayment()
    {
        return $this->hasMany(AccountReceivablePayment::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}
