<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            if (Schema::hasColumn('organizations', 'domain')) {
                $table->dropColumn('domain');
            }
            if (Schema::hasColumn('organizations', 'wazzup24_webhook_secret')) {
                $table->dropColumn('wazzup24_webhook_secret');
            }
            if (Schema::hasColumn('organizations', 'wazzup24_settings')) {
                $table->dropColumn('wazzup24_settings');
            }
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            if (!Schema::hasColumn('organizations', 'domain')) {
                $table->string('domain')->nullable();
            }
            if (!Schema::hasColumn('organizations', 'wazzup24_webhook_secret')) {
                $table->string('wazzup24_webhook_secret')->nullable();
            }
            if (!Schema::hasColumn('organizations', 'wazzup24_settings')) {
                $table->json('wazzup24_settings')->nullable();
            }
        });
    }
};


