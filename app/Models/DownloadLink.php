<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DownloadLink extends Model
{
    //
    protected $fillable = ['product_id', 'wx_nickname', 'unique_token', 'download_count', 'max_downloads', 'expire_at', 'status'];
    // 建立关联：一条发货链接属于一个商品
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
