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
        // Users table indexes
        Schema::table('users', function (Blueprint $table) {
            $table->index('email');
            $table->index('role');
            $table->index('is_active');
            $table->index('created_at');
            $table->index(['role', 'is_active']);
        });

        // Categories table indexes
        Schema::table('categories', function (Blueprint $table) {
            $table->index('slug');
            $table->index('created_by');
            $table->index('is_active');
            $table->index('sort_order');
            $table->index(['is_active', 'sort_order']);
        });

        // Exams table indexes
        Schema::table('exams', function (Blueprint $table) {
            $table->index('slug');
            $table->index('category_id');
            $table->index('created_by');
            $table->index('is_active');
            $table->index('is_public');
            $table->index('difficulty_level');
            $table->index('start_time');
            $table->index('end_time');
            $table->index('created_at');
            $table->index(['is_active', 'is_public']);
            $table->index(['category_id', 'is_active']);
            $table->index(['created_by', 'is_active']);
        });

        // Questions table indexes
        Schema::table('questions', function (Blueprint $table) {
            $table->index('exam_id');
            $table->index('question_type');
            $table->index('order');
            $table->index('is_required');
            $table->index(['exam_id', 'order']);
        });

        // Answers table indexes
        Schema::table('answers', function (Blueprint $table) {
            $table->index('question_id');
            $table->index('is_correct');
            $table->index('order');
            $table->index(['question_id', 'is_correct']);
        });

        // Classes table indexes
        Schema::table('classes', function (Blueprint $table) {
            $table->index('teacher_id');
            $table->index('join_code');
            $table->index('is_active');
            $table->index('require_approval');
            $table->index('created_at');
            $table->index(['teacher_id', 'is_active']);
        });

        // Exam attempts table indexes
        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->index('exam_id');
            $table->index('user_id');
            $table->index('percentage');
            $table->index('started_at');
            $table->index('completed_at');
            $table->index('created_at');
            $table->index(['user_id', 'exam_id']);
            $table->index(['exam_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
        });

        // Exam attempt answers table indexes
        Schema::table('exam_attempt_answers', function (Blueprint $table) {
            $table->index('attempt_id');
            $table->index('question_id');
            $table->index('answer_id');
            $table->index('is_correct');
            $table->index(['attempt_id', 'question_id']);
        });

        // Exam violations table indexes
        Schema::table('exam_violations', function (Blueprint $table) {
            $table->index('attempt_id');
            $table->index('user_id');
            $table->index('exam_id');
            $table->index('violation_type');
            $table->index('severity');
            $table->index('violated_at');
            $table->index(['exam_id', 'violated_at']);
            $table->index(['user_id', 'exam_id']);
            $table->index(['severity', 'violated_at']);
        });

        // Class join requests table indexes
        Schema::table('class_join_requests', function (Blueprint $table) {
            $table->index('class_id');
            $table->index('student_id');
            $table->index('status');
            $table->index('decided_by');
            $table->index('created_at');
            $table->index(['class_id', 'status']);
            $table->index(['student_id', 'status']);
        });

        // Class user pivot table indexes
        Schema::table('class_user', function (Blueprint $table) {
            $table->index('class_id');
            $table->index('user_id');
            $table->index('joined_at');
            $table->index(['class_id', 'user_id']);
        });

        // Class exam pivot table indexes
        Schema::table('class_exam', function (Blueprint $table) {
            $table->index('class_id');
            $table->index('exam_id');
            $table->index('created_at');
            $table->index(['class_id', 'exam_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropIndex(['role']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['role', 'is_active']);
        });

        // Categories table
        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropIndex(['created_by']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['sort_order']);
            $table->dropIndex(['is_active', 'sort_order']);
        });

        // Exams table
        Schema::table('exams', function (Blueprint $table) {
            $table->dropIndex(['slug']);
            $table->dropIndex(['category_id']);
            $table->dropIndex(['created_by']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['is_public']);
            $table->dropIndex(['difficulty_level']);
            $table->dropIndex(['start_time']);
            $table->dropIndex(['end_time']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['is_active', 'is_public']);
            $table->dropIndex(['category_id', 'is_active']);
            $table->dropIndex(['created_by', 'is_active']);
        });

        // Questions table
        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex(['exam_id']);
            $table->dropIndex(['question_type']);
            $table->dropIndex(['order']);
            $table->dropIndex(['is_required']);
            $table->dropIndex(['exam_id', 'order']);
        });

        // Answers table
        Schema::table('answers', function (Blueprint $table) {
            $table->dropIndex(['question_id']);
            $table->dropIndex(['is_correct']);
            $table->dropIndex(['order']);
            $table->dropIndex(['question_id', 'is_correct']);
        });

        // Classes table
        Schema::table('classes', function (Blueprint $table) {
            $table->dropIndex(['teacher_id']);
            $table->dropIndex(['join_code']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['require_approval']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['teacher_id', 'is_active']);
        });

        // Exam attempts table
        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->dropIndex(['exam_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['percentage']);
            $table->dropIndex(['started_at']);
            $table->dropIndex(['completed_at']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['user_id', 'exam_id']);
            $table->dropIndex(['exam_id', 'created_at']);
            $table->dropIndex(['user_id', 'created_at']);
        });

        // Exam attempt answers table
        Schema::table('exam_attempt_answers', function (Blueprint $table) {
            $table->dropIndex(['attempt_id']);
            $table->dropIndex(['question_id']);
            $table->dropIndex(['answer_id']);
            $table->dropIndex(['is_correct']);
            $table->dropIndex(['attempt_id', 'question_id']);
        });

        // Exam violations table
        Schema::table('exam_violations', function (Blueprint $table) {
            $table->dropIndex(['attempt_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['exam_id']);
            $table->dropIndex(['violation_type']);
            $table->dropIndex(['severity']);
            $table->dropIndex(['violated_at']);
            $table->dropIndex(['exam_id', 'violated_at']);
            $table->dropIndex(['user_id', 'exam_id']);
            $table->dropIndex(['severity', 'violated_at']);
        });

        // Class join requests table
        Schema::table('class_join_requests', function (Blueprint $table) {
            $table->dropIndex(['class_id']);
            $table->dropIndex(['student_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['decided_by']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['class_id', 'status']);
            $table->dropIndex(['student_id', 'status']);
        });

        // Class user pivot table
        Schema::table('class_user', function (Blueprint $table) {
            $table->dropIndex(['class_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['joined_at']);
            $table->dropIndex(['class_id', 'user_id']);
        });

        // Class exam pivot table
        Schema::table('class_exam', function (Blueprint $table) {
            $table->dropIndex(['class_id']);
            $table->dropIndex(['exam_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['class_id', 'exam_id']);
        });
    }
};
