<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Обновляем поля временных меток в таблице messages для поддержки миллисекунд
        Schema::table('messages', function (Blueprint $table) {
            $table->timestamp('created_at', 3)->nullable()->change(); // 3 = миллисекунды
            $table->timestamp('updated_at', 3)->nullable()->change();
            $table->timestamp('hidden_at', 3)->nullable()->change();
        });

        // Обновляем поля временных меток в таблице chats для поддержки миллисекунд
        Schema::table('chats', function (Blueprint $table) {
            $table->timestamp('created_at', 3)->nullable()->change();
            $table->timestamp('updated_at', 3)->nullable()->change();
            $table->timestamp('closed_at', 3)->nullable()->change();
            $table->timestamp('last_activity_at', 3)->nullable()->change();
            $table->timestamp('deleted_at', 3)->nullable()->change();
        });

        // Обновляем поля временных меток в таблице message_reads для поддержки миллисекунд
        Schema::table('message_reads', function (Blueprint $table) {
            $table->timestamp('read_at', 3)->nullable()->change();
            $table->timestamp('created_at', 3)->nullable()->change();
            $table->timestamp('updated_at', 3)->nullable()->change();
        });

        // Обновляем поля временных меток в таблице chat_history для поддержки миллисекунд
        Schema::table('chat_history', function (Blueprint $table) {
            $table->timestamp('created_at', 3)->nullable()->change();
            $table->timestamp('updated_at', 3)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Возвращаем стандартную точность временных меток
        Schema::table('messages', function (Blueprint $table) {
            $table->timestamp('created_at')->nullable()->change();
            $table->timestamp('updated_at')->nullable()->change();
            $table->timestamp('hidden_at')->nullable()->change();
        });

        Schema::table('chats', function (Blueprint $table) {
            $table->timestamp('created_at')->nullable()->change();
            $table->timestamp('updated_at')->nullable()->change();
            $table->timestamp('closed_at')->nullable()->change();
            $table->timestamp('last_activity_at')->nullable()->change();
            $table->timestamp('deleted_at')->nullable()->change();
        });

        Schema::table('message_reads', function (Blueprint $table) {
            $table->timestamp('read_at')->nullable()->change();
            $table->timestamp('created_at')->nullable()->change();
            $table->timestamp('updated_at')->nullable()->change();
        });

        Schema::table('chat_history', function (Blueprint $table) {
            $table->timestamp('created_at')->nullable()->change();
            $table->timestamp('updated_at')->nullable()->change();
        });
    }
};