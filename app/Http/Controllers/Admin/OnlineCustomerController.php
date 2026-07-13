<?php

namespace App\Http\Controllers\Admin;

use App\Events\MessageSent;
use App\Models\OnlineCustomer;
use Illuminate\Http\Request;

class OnlineCustomerController
{
    //
    public function index()
    {
        return view('admin.online_customer');
    }
    //发送消息
    public function send(Request $request)
    {
        $message = OnlineCustomer::create([
            'user_id' => $request->user_id,
            'customer_id' => $request->customer_id ?? 1, // 临时写死或取当前登录客服
            'sender_type' => $request->sender_type,     // 1=用户, 2=客服
            'content' => $request->content,
            'msg_type' => 'text',
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'code' => 200,
            'data' => $message
        ]);

    }

    //接受消息
    public function reveice()
    {

    }
}
