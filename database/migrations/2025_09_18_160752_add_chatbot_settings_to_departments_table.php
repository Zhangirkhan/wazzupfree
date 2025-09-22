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
        Schema::table('departments', function (Blueprint $table) {
            $table->boolean('show_in_chatbot')->default(false)->comment('Показывать отдел в чат-боте');
            $table->integer('chatbot_order')->default(0)->comment('Порядок сортировки в чат-боте');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn(['show_in_chatbot', 'chatbot_order']);
        });
    }
};
