<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chat_message', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->index()->comment('用户ID');
            $table->integer('customer_id')->nullable()->index()->comment('客服ID(系统自动分配前可为空)');
            $table->tinyInteger('sender_type')->comment('发送方类型: 1=用户, 2=客服');
            $table->text('content')->comment('消息内容');
            $table->string('msg_type', 20)->default('text')->comment('消息类型: text=文本, image=图片, file=文件');
            $table->tinyInteger('is_read')->default(0)->comment('是否已读: 0=未读, 1=已读');
            $table->tinyInteger('is_destruct_after_reading')->default(0)->comment('是否阅后即焚: 0=否, 1=是');
            $table->integer('order_id')->nullable()->comment('关联订单ID');
            $table->tinyInteger('is_withdrawn')->default(0)->comment('是否撤回: 0=否, 1=是');
            $table->timestamp('withdrawn_at')->nullable()->comment('撤回时间');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_message');
    }
};
