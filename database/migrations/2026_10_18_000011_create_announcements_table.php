<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('tieu_de', 200);
            $table->text('noi_dung');
            $table->foreignId('nguoi_gui_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('doi_tuong', 20)->default('toan_truong');
            $table->foreignId('khoa_id')->nullable()->constrained('faculties')->nullOnDelete();
            $table->foreignId('lop_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->foreignId('lop_hp_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->foreignId('nguoi_nhan_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('doi_tuong');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
