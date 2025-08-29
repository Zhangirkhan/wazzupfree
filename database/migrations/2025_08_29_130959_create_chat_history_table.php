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
        Schema::create('chat_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_id')->constrained('chats')->onDelete('cascade');
            $table->string('action'); // 'department_selected', 'assigned_to', 'completed', 'reset'
            $table->string('description'); // Краткое описание действия
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // Кто выполнил действие
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null'); // Отдел
            $table->json('metadata')->nullable(); // Дополнительные данные
            $table->timestamps();
            
            $table->index(['chat_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_history');
    }
};
