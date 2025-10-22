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
        Schema::create('exam_violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('exam_attempts')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            
            // Violation details
            $table->enum('violation_type', [
                'tab_switch',           // Chuyển tab
                'copy_paste',           // Copy/Paste
                'fullscreen_exit',      // Thoát fullscreen
                'mouse_leave',          // Chuột rời khỏi màn hình
                'right_click',          // Click chuột phải
                'keyboard_shortcut',    // Phím tắt đáng ngờ
                'time_anomaly',         // Thời gian bất thường
                'multiple_devices',     // Nhiều thiết bị
                'suspicious_pattern'    // Hành vi đáng ngờ
            ]);
            
            $table->text('description')->nullable();
            $table->json('metadata')->nullable(); // Lưu thêm thông tin chi tiết
            $table->integer('severity')->default(1); // 1=Low, 2=Medium, 3=High, 4=Critical
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('violated_at');
            
            $table->timestamps();
            
            // Indexes
            $table->index(['attempt_id', 'violation_type']);
            $table->index(['user_id', 'violated_at']);
            $table->index('severity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_violations');
    }
};
