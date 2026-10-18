<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faculty_id')->nullable()->constrained('faculties')->nullOnDelete();
            $table->string('ma_mh', 20)->unique();
            $table->string('ten_mh', 150);
            $table->unsignedTinyInteger('so_tin_chi')->default(3);
            $table->string('loai_mh', 20)->default('bat_buoc');
            $table->unsignedTinyInteger('so_tiet_ly_thuyet')->nullable();
            $table->unsignedTinyInteger('so_tiet_thuc_hanh')->nullable();
            $table->timestamps();

            $table->index(['faculty_id', 'loai_mh']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
