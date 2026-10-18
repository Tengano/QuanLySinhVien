<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained('users')->nullOnDelete();
            $table->string('ma_sv', 20)->unique();
            $table->string('ho_ten', 150);
            $table->date('ngay_sinh');
            $table->string('gioi_tinh', 10)->default('nam');
            $table->foreignId('lop_id')->constrained('classes')->restrictOnDelete();
            $table->string('email', 150)->nullable();
            $table->string('sdt', 15)->nullable();
            $table->string('cccd', 20)->nullable()->unique();
            $table->text('dia_chi')->nullable();
            $table->string('anh')->nullable();
            $table->string('trang_thai', 20)->default('dang_hoc');
            $table->date('ngay_nhap_hoc')->nullable();
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('ho_ten');
            $table->index('email');
            $table->index(['lop_id', 'trang_thai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
