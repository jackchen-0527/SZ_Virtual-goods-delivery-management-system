<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    //
    protected $fillable = ['product_id', 'wx_nickname', 'unique_token', 'download_count', 'max_downloads', 'expire_at', 'status'];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
