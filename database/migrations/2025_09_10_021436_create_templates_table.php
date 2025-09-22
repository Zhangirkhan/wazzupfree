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
        Schema::create('templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('content');
            $table->enum('type', ['message', 'email', 'sms', 'notification'])->default('message');
            $table->enum('category', ['greeting', 'farewell', 'support', 'sales', 'technical', 'general'])->default('general');
            $table->json('variables')->nullable(); // Переменные для подстановки
            $table->string('language', 5)->default('ru'); // Язык шаблона
            $table->boolean('is_active')->default(true);
            $table->boolean('is_system')->default(false); // Системный шаблон
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('organization_id')->nullable()->constrained()->onDelete('cascade');
            $table->integer('usage_count')->default(0); // Счетчик использования
            $table->timestamps();

            // Индексы
            $table->index(['type', 'is_active']);
            $table->index(['category', 'is_active']);
            $table->index(['organization_id', 'is_active']);
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
