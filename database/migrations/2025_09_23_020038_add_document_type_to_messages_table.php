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
        DB::statement('ALTER TABLE messages DROP CONSTRAINT IF EXISTS messages_type_check');
        
        // Добавляем новое ограничение с типом document
        DB::statement("ALTER TABLE messages ADD CONSTRAINT messages_type_check CHECK (type IN ('text', 'system', 'file', 'image', 'video', 'document', 'audio'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Удаляем новое ограничение
        DB::statement('ALTER TABLE messages DROP CONSTRAINT IF EXISTS messages_type_check');
        
        // Возвращаем старое ограничение
        DB::statement("ALTER TABLE messages ADD CONSTRAINT messages_type_check CHECK (type IN ('text', 'system', 'file', 'image', 'video'))");
    }
};