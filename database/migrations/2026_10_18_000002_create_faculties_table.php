<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faculties', function (Blueprint $table) {
            $table->id();
            $table->string('ma_khoa', 20)->unique();
            $table->string('ten_khoa', 150);
            $table->string('truong_khoa', 150)->nullable();
            $table->string('dien_thoai', 15)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('dia_chi')->nullable();
            $table->timestamps();

            $table->index('ten_khoa');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faculties');
    }
};
