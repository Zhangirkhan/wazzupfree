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
            $table->boolean('is_from_client')->default(false)->comment('Сообщение от клиента (true) или от бота/сотрудника (false)');
            $table->string('messenger_message_id')->nullable()->comment('ID сообщения в мессенджере (Wazzup24)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['is_from_client', 'messenger_message_id']);
        });
    }
};
