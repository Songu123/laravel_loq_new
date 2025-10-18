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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->text('question_text');
            $table->enum('question_type', ['multiple_choice', 'true_false', 'short_answer', 'essay'])->default('multiple_choice');
            $table->decimal('marks', 5, 2)->default(1.00);
            $table->boolean('is_required')->default(true);
            $table->text('explanation')->nullable();
            $table->integer('order')->default(0);
            $table->timestamps();

            // Indexes
            $table->index(['exam_id']);
            $table->index(['question_type']);
            $table->index(['order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
