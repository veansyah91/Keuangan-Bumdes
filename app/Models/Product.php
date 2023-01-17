<?php

namespace App\Models;

use App\Models\Stock;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'category', 'brand', 'supplier', 'unit_price', 'selling_price', 'business_id', 'is_active', 'is_stock_checked', 'unit'];

    public function stocks()
    {
        $stocks = $this->hasMany(Stock::class);
        return $stocks;
    }

    public function getTotal()
    {
        $debitStocks = Stock::where('product_id', $this->id)->where('debit', '>', 0)->get();
        $creditStocks = Stock::where('product_id', $this->id)->where('credit', '>', 0)->get();

        return [
            'total_qty' => $debitStocks->sum('qty') + $creditStocks->sum('qty'),
            'total' => $debitStocks->sum('debit') - $creditStocks->sum('credit'),
        ];
    }

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class)->withPivot('qty', 'value');
    }


    public function scopeFilter($query, array $filters)
    {
        // filter search
        $query->when($filters['search'] ?? false, function($query, $search){
            return $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('code', 'like', '%' . $search . '%')
                        ->orWhere('category', 'like', '%' . $search . '%')
                        ->orWhere('supplier', 'like', '%' . $search . '%')
                        ->orWhere('selling_price', 'like', '%' . $search . '%')
                        ->orWhere('unit_price', 'like', '%' . $search . '%');
        });
    }

    public function scopeCheckStock($query, array $filters)
    {
        // filter check stock
        $query->when($filters['stock_check'] ?? false, function($query, $stock_check){
            return $query->where('is_stock_checked', true);
        });
    }
}
