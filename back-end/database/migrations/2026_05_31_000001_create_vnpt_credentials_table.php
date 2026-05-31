<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vnpt_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete()->comment('Người dùng sở hữu');
            $table->string('username')->comment('Tài khoản VNPT Office');
            $table->string('password')->comment('Mật khẩu VNPT Office');
            $table->string('device_name')->nullable()->comment('Tên thiết bị (VD: iPhone 17)');
            $table->string('device_type')->default('IOS')->comment('Loại thiết bị (IOS/ANDROID)');
            $table->boolean('is_active')->default(true)->comment('Đang hoạt động');
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vnpt_credentials');
    }
};
