<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAccessKey extends Model
{
    //
    protected $table = 'stock_access_key';
    protected $fillable = [
        'product_id',
        'access_key',
        'status',
        'used_from_platform_name',
        'used_from_platform_order_id',
        'used_ip',
        'used_device_fingerprint',
        'used_at'
    ];
}
