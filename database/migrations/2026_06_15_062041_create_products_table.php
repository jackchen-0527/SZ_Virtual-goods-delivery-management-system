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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type')->default(1)->comment('产品类型(1:exe软件 2:web网页成品 3:程序脚本)');
            $table->string('name')->comment('产品名称');
            $table->string('version')->comment('产品版本');
            $table->string('file_path', 500)->default('')->comment('产品路径');
            $table->string('decompress_password')->comment('解压密码');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
