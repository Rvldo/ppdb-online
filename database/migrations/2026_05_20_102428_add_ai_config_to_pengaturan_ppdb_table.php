<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengaturan_ppdb', function (Blueprint $table) {
            $table->string('ai_provider', 20)->default('deepseek')->after('footer_text');
            $table->text('ai_api_key')->nullable()->after('ai_provider');
            $table->string('ai_model')->nullable()->after('ai_api_key');
        });
    }

    public function down(): void
    {
        Schema::table('pengaturan_ppdb', function (Blueprint $table) {
            $table->dropColumn(['ai_provider', 'ai_api_key', 'ai_model']);
        });
    }
};
