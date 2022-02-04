<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['nama_produk', 'kode', 'kategori', 'brand', 'pemasok', 'modal', 'jual', 'business_id'];

    public function stock()
    {
        return $this->hasOne(Stock::class);
    }

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class)->withPivot('jumlah', 'harga');
    }
}
