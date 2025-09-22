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
        // Удаляем старое ограничение
        DB::statement("ALTER TABLE chats DROP CONSTRAINT IF EXISTS chats_messenger_status_check");

        // Добавляем новое ограничение с 'closed'
        DB::statement("ALTER TABLE chats ADD CONSTRAINT chats_messenger_status_check CHECK (messenger_status::text = ANY (ARRAY['menu'::character varying, 'department_selected'::character varying, 'active'::character varying, 'completed'::character varying, 'closed'::character varying]::text[]))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // В PostgreSQL нельзя удалить значение из enum после добавления
        // Поэтому оставляем как есть или пересоздаем enum
        // Для простоты оставляем значение 'closed' в enum
    }
};
