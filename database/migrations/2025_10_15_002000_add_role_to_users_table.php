<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'teacher', 'student'])->default('student')->after('email_verified_at');
            $table->string('student_id')->nullable()->unique()->after('role');
            $table->string('phone')->nullable()->after('student_id');
            $table->string('address')->nullable()->after('phone');
            $table->text('bio')->nullable()->after('address');
            $table->string('avatar')->nullable()->after('bio');
            $table->boolean('is_active')->default(true)->after('avatar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'student_id', 'phone', 'address', 'bio', 'avatar', 'is_active']);
        });
    }
};