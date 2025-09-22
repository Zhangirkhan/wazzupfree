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
        // Изменяем enum для добавления типа 'video'
        DB::statement("ALTER TABLE messages DROP CONSTRAINT messages_type_check");
        DB::statement("ALTER TABLE messages ADD CONSTRAINT messages_type_check CHECK (type IN ('text', 'system', 'file', 'image', 'video'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Возвращаем старый enum
        DB::statement("ALTER TABLE messages DROP CONSTRAINT messages_type_check");
        DB::statement("ALTER TABLE messages ADD CONSTRAINT messages_type_check CHECK (type IN ('text', 'system', 'file', 'image'))");
    }
};
