<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('office_documents', function (Blueprint $table) {
            $table->longText('trich_yeu')->nullable()->change();
            $table->string('so_ki_hieu', 500)->nullable()->change();
            $table->string('co_quan_ban_hanh', 500)->nullable()->change();
            $table->string('process_definition_id', 500)->nullable()->change();
            $table->string('process_instance_id', 500)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('office_documents', function (Blueprint $table) {
            $table->string('trich_yeu')->nullable()->change();
            $table->string('so_ki_hieu')->nullable()->change();
            $table->string('co_quan_ban_hanh')->nullable()->change();
            $table->string('process_definition_id')->nullable()->change();
            $table->string('process_instance_id')->nullable()->change();
        });
    }
};
