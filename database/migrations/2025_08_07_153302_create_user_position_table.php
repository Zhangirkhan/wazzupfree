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
        Schema::create('user_position', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('position_id')->constrained()->onDelete('cascade');
            $table->foreignId('organization_id')->nullable()->constrained()->onDelete('cascade'); // Должность в конкретной организации
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('cascade'); // Должность в конкретном отделе
            $table->boolean('is_primary')->default(false); // Основная должность пользователя
            $table->date('assigned_at'); // Дата назначения
            $table->date('expires_at')->nullable(); // Дата окончания (если временная должность)
            $table->timestamps();
            
            // Уникальный индекс для предотвращения дублирования
            $table->unique(['user_id', 'position_id', 'organization_id', 'department_id'], 'user_position_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_position');
    }
};
