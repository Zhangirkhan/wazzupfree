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
        Schema::create('contractors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('type', ['legal', 'individual'])->default('legal');

            // Для юридических лиц
            $table->string('inn', 12)->nullable();
            $table->string('kpp', 9)->nullable();
            $table->string('ogrn', 13)->nullable();
            $table->text('legal_address')->nullable();
            $table->text('actual_address')->nullable();

            // Для физических лиц
            $table->string('passport_series', 4)->nullable();
            $table->string('passport_number', 6)->nullable();
            $table->string('passport_issued_by')->nullable();
            $table->date('passport_issued_date')->nullable();
            $table->text('address')->nullable();

            // Контактная информация
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->string('contact_email')->nullable();

            // Банковские реквизиты (для юр.лиц)
            $table->string('bank_name')->nullable();
            $table->string('bank_account', 20)->nullable();
            $table->string('bik', 9)->nullable();

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Индексы
            $table->index(['type', 'is_active']);
            $table->index('inn');
            $table->unique('inn');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contractors');
    }
};
