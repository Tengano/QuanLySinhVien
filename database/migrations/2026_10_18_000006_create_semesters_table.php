<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->string('ten_hoc_ky', 20);
            $table->unsignedTinyInteger('hoc_ky_so');
            $table->string('nam_hoc', 9);
            $table->date('ngay_bat_dau');
            $table->date('ngay_ket_thuc');
            $table->timestamps();

            $table->unique(['nam_hoc', 'hoc_ky_so']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('semesters');
    }
};
