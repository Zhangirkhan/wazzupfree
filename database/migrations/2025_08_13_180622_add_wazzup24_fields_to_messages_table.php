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
        Schema::table('messages', function (Blueprint $table) {
            $table->string('wazzup_message_id')->nullable()->after('content');
            $table->enum('direction', ['in', 'out'])->default('out')->after('wazzup_message_id');
            $table->string('status')->default('sent')->after('direction');
            
            $table->index('wazzup_message_id');
            $table->index(['direction', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex(['wazzup_message_id']);
            $table->dropIndex(['direction', 'status']);
            $table->dropColumn(['wazzup_message_id', 'direction', 'status']);
        });
    }
};
