<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('course_id')->constrained('courses')->cascadeOnDelete();
            $table->string('trang_thai', 20)->default('dang_hoc');
            $table->unsignedTinyInteger('lan_hoc')->default(1);
            $table->timestamps();

            $table->unique(['student_id', 'course_id', 'lan_hoc']);
            $table->index(['course_id', 'trang_thai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
