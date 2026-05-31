<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('office_documents', function (Blueprint $table) {
            $table->id();
            $table->string('external_id')->unique()->comment('ID từ OfficeVNPT');
            $table->string('trich_yeu')->nullable()->comment('Trích yếu');
            $table->string('so_ki_hieu')->nullable()->comment('Số ký hiệu');
            $table->string('co_quan_ban_hanh')->nullable()->comment('Cơ quan ban hành');
            $table->date('ngay_van_ban')->nullable()->comment('Ngày văn bản');
            $table->dateTime('han_xu_ly')->nullable()->comment('Hạn xử lý');
            $table->dateTime('ngay_den_di')->nullable()->comment('Ngày đến đi');
            $table->dateTime('ngay_nhan')->nullable()->comment('Ngày nhận');
            $table->string('do_khan')->nullable()->comment('Độ khẩn');
            $table->string('cong_van_den_di')->default('1')->comment('Công văn đến/đi');
            $table->string('process_definition_id')->nullable()->comment('Process Definition ID');
            $table->string('process_instance_id')->nullable()->comment('Process Instance ID');
            $table->boolean('is_read')->default(false)->comment('Đã đọc');
            $table->string('type')->nullable()->comment('Loại văn bản');
            $table->json('raw_data')->nullable()->comment('Raw data từ API');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete()->comment('Người dùng sở hữu');
            $table->timestamps();

            $table->index('external_id');
            $table->index('ngay_nhan');
            $table->index('do_khan');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('office_documents');
    }
};
