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
        Schema::create('require_issues_report', function (Blueprint $table) {
            $table->id();

            $table->string('name', 150)->comment('需求名称');

            $table->unsignedTinyInteger('type')->default(1)->comment('需求类型(1:新需求,2:Bug修复,3:优化建议');

            $table->unsignedTinyInteger('level')->default(1)->comment('需求紧急程度(1:低, 2:中, 3:高, 4:致命)');

            $table->unsignedTinyInteger('status')->default(0)->comment('需求状态(0:待评审/未开始, 1:开发中, 2:待测试, 3:已上线, 4:已拒绝)');

            $table->string('sponsor', 50)->comment('需求发起人');

            $table->unsignedBigInteger('assigned_to_user_id')->nullable()->comment('当前指派的处理人/开发人员ID');

            $table->unsignedBigInteger('handler_user_id')->nullable()->comment('最终解决此问题的人员ID');

            $table->timestamp('resolved_at')->nullable()->comment('问题解决/需求上线时间');

            $table->text('description')->comment('需求具体说明');

            $table->string('attachments', 512)->nullable()->comment('附件/报错截图');

            $table->string('reported_ip', 45)->nullable()->comment('上报人的IP地址');

            $table->string('reported_device_fingerprint', 255)->nullable()->comment('上报人的浏览器环境指纹');

            $table->timestamps();

            $table->index(['status', 'level']);

            $table->index('assigned_to_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('require_issues_report');
    }
};
