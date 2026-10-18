<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->unique()->constrained('enrollments')->cascadeOnDelete();
            $table->decimal('diem_qua_trinh', 4, 2)->nullable();
            $table->decimal('diem_thi', 4, 2)->nullable();
            $table->decimal('diem_tb', 4, 2)->nullable();
            $table->string('diem_chu', 3)->nullable();
            $table->decimal('diem_he4', 3, 2)->nullable();
            $table->boolean('dat')->nullable();
            $table->foreignId('nguoi_nhap_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('nhap_luc')->nullable();
            $table->timestamp('khoa_luc')->nullable();
            $table->text('ghi_chu')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grades');
    }
};
