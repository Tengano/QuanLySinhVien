<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('majors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faculty_id')->constrained('faculties')->restrictOnDelete();
            $table->string('ma_nganh', 20)->unique();
            $table->string('ten_nganh', 150);
            $table->unsignedTinyInteger('so_nam_dao_tao')->default(4);
            $table->timestamps();

            $table->unique(['faculty_id', 'ten_nganh']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('majors');
    }
};
