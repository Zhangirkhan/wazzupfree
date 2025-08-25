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
        Schema::table('chats', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('messenger_status', ['menu', 'department_selected', 'active', 'completed'])->default('menu');
            $table->timestamp('last_activity_at')->nullable();
            $table->boolean('is_messenger_chat')->default(false);
            $table->string('messenger_phone')->nullable();
            $table->json('messenger_data')->nullable(); // Для хранения состояния бота
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropColumn([
                'department_id',
                'messenger_status',
                'last_activity_at',
                'is_messenger_chat',
                'messenger_phone',
                'messenger_data'
            ]);
        });
    }
};
