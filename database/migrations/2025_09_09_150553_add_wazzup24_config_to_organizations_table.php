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
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('wazzup24_api_key')->nullable();
            $table->string('wazzup24_channel_id')->nullable();
            $table->string('wazzup24_webhook_url')->nullable();
            $table->string('wazzup24_webhook_secret')->nullable();
            $table->json('wazzup24_settings')->nullable();
            $table->boolean('wazzup24_enabled')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn([
                'wazzup24_api_key',
                'wazzup24_channel_id',
                'wazzup24_webhook_url',
                'wazzup24_webhook_secret',
                'wazzup24_settings',
                'wazzup24_enabled'
            ]);
        });
    }
};
