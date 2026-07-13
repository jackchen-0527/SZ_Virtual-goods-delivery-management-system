<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OnlineCustomer extends Model
{
    //
    protected $table = 'chat_message';
    protected $fillable = [
        'user_id',
        'customer_id',
        'sender_type',
        'content',
        'msg_type',
        'is_read',
        'is_destruct_after_reading',
        'order_id',
        'is_withdrawn',
        'withdrawn_a',
        'deleted_at',
    ];
}
