<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Dùng string + enum PHP thay vì ENUM của MySQL để test SQLite chạy được (ADR-07).
            $table->string('role', 20)->default('sinh_vien');
            $table->boolean('trang_thai')->default(true);
            $table->string('so_dien_thoai', 15)->nullable();
            $table->string('avatar')->nullable();
            $table->timestamp('last_login_at')->nullable();

            $table->index('role');
            $table->index(['role', 'trang_thai']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role', 'trang_thai']);
            $table->dropIndex(['role']);
            $table->dropColumn(['role', 'trang_thai', 'so_dien_thoai', 'avatar', 'last_login_at']);
        });
    }
};
