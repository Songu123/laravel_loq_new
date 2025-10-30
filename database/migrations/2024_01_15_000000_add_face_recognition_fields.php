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
        // Add face recognition fields to users table
        Schema::table('users', function (Blueprint $table) {
            $table->json('face_embeddings')->nullable()->after('avatar');
            $table->timestamp('face_enrolled_at')->nullable()->after('face_embeddings');
            $table->string('face_images')->nullable()->after('face_enrolled_at')->comment('Comma-separated image paths');
        });

        // Create face_login_attempts table
        Schema::create('face_login_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('ip_address', 45);
            $table->text('user_agent');
            $table->boolean('success')->default(false);
            $table->decimal('confidence', 3, 2)->default(0.00);
            $table->boolean('is_live')->default(false);
            $table->string('image_path')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('created_at')->useCurrent();
            
            $table->index('user_id');
            $table->index('created_at');
            $table->index('success');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('face_login_attempts');
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['face_embeddings', 'face_enrolled_at', 'face_images']);
        });
    }
};
