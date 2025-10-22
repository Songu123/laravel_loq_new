<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('exam_tab_events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('exam_id')->index();
            $table->unsignedBigInteger('attempt_id')->index();
            $table->string('event_type'); // 'tab_switch', 'window_blur', 'window_focus', 'visibility_change'
            $table->json('event_data'); // {action: 'blur/focus/hidden/visible', timestamp, question_id, etc}
            $table->string('ip_address');
            $table->text('user_agent');
            $table->timestamp('occurred_at');
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('exam_id')->references('id')->on('exams');
            $table->foreign('attempt_id')->references('id')->on('exam_attempts');
            
            $table->index(['attempt_id', 'event_type']);
            $table->index(['exam_id', 'occurred_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('exam_tab_events');
    }
};