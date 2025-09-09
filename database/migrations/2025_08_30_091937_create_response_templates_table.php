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
        Schema::create('response_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Название шаблона
            $table->text('content'); // Содержимое шаблона
            $table->string('category')->default('general'); // Категория (приветствие, прощание, помощь и т.д.)
            $table->boolean('is_active')->default(true); // Активен ли шаблон
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // Кто создал
            $table->foreignId('organization_id')->constrained()->onDelete('cascade'); // К какой организации относится
            $table->integer('usage_count')->default(0); // Количество использований
            $table->timestamps();
            
            // Индексы
            $table->index(['organization_id', 'category']);
            $table->index(['organization_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('response_templates');
    }
};
