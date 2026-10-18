<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('ma_lhp', 30)->unique();
            $table->foreignId('subject_id')->constrained('subjects')->restrictOnDelete();
            $table->foreignId('semester_id')->constrained('semesters')->restrictOnDelete();
            $table->foreignId('giang_vien_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('phong_hoc', 30)->nullable();
            $table->string('lich_hoc', 100)->nullable();
            $table->unsignedSmallInteger('so_sv_toi_da')->default(50);
            $table->string('trang_thai', 20)->default('mo');
            $table->text('ghi_chu')->nullable();
            $table->timestamps();

            $table->index(['semester_id', 'subject_id']);
            $table->index(['giang_vien_id', 'trang_thai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
