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
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->integer('duration_minutes')->default(60);
            $table->integer('total_questions')->default(0);
            $table->decimal('total_marks', 8, 2)->default(0);
            $table->enum('difficulty_level', ['easy', 'medium', 'hard'])->default('medium');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_public')->default(false);
            $table->datetime('start_time')->nullable();
            $table->datetime('end_time')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['category_id']);
            $table->index(['created_by']);
            $table->index(['is_active']);
            $table->index(['is_public']);
            $table->index(['difficulty_level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
