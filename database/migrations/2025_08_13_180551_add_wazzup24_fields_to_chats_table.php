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
            $table->string('phone')->nullable()->after('description');
            $table->string('wazzup_chat_id')->nullable()->after('phone');
            $table->unsignedBigInteger('creator_id')->nullable()->after('wazzup_chat_id');
            
            $table->index(['phone', 'type']);
            $table->index('wazzup_chat_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chats', function (Blueprint $table) {
            $table->dropIndex(['phone', 'type']);
            $table->dropIndex(['wazzup_chat_id']);
            $table->dropColumn(['phone', 'wazzup_chat_id', 'creator_id']);
        });
    }
};
