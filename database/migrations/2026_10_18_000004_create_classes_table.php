<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('major_id')->constrained('majors')->restrictOnDelete();
            $table->string('ma_lop', 30)->unique();
            $table->string('ten_lop', 150);
            $table->unsignedSmallInteger('khoa_hoc');
            $table->foreignId('gvcn_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['major_id', 'khoa_hoc']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
