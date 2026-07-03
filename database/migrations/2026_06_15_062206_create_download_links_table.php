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
        Schema::create('download_links', function (Blueprint $table) {
            $table->id();
            // 关联商品表，商品删了对应的发货链接自动级联删除
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('wx_nickname')->comment('微信备注');
            $table->string('unique_token', 64)->comment('提取Token');
            $table->integer('download_count')->comment('已下载次数');
            $table->integer('max_downloads')->default(3)->comment('最大下载限额');
            $table->timestamp('expire_at')->comment('到期时间');
            $table->tinyInteger('status')->default(1)->comment('1:有效 0:失效 2:耗尽');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('download_links');
    }
};
