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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('content');
            $table->enum('type', ['text', 'system', 'file', 'image'])->default('text');
            $table->json('metadata')->nullable(); // Для дополнительных данных (файлы, ссылки и т.д.)
            $table->boolean('is_hidden')->default(false); // Soft delete для подчиненных
            $table->foreignId('hidden_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('hidden_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
