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
        Schema::create('stock_access_key', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->comment('关联产品ID');
            $table->string('access_key', 64)->unique()->comment('卡密密钥');
            $table->tinyInteger('status')->default(0)->comment('状态(0:未分配/未使用, 1:已分配/已核销, 2:已失效/已作废');
            $table->string('used_from_platform_name', 20)->nullable()->comment('对应的电商平台名称');
            $table->string('used_from_platform_order_id', 40)->nullable()->comment('对应的电商平台订单号');
            $table->string('used_ip', 45)->nullable()->comment('使用者的IP地址');
            $table->string('used_device_fingerprint', 255)->nullable()->comment('使用者的浏览器环境指纹');
            $table->timestamp('used_at')->nullable()->comment('卡密被核销使用的时间');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['product_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_access_key');
    }
};
