<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengaturan_ppdb', function (Blueprint $table) {
            // Branding
            $table->string('logo_path')->nullable()->after('nama_sekolah');
            $table->string('favicon_path')->nullable()->after('logo_path');
            $table->string('singkatan_sekolah', 10)->nullable()->after('favicon_path');
            $table->string('alamat_sekolah')->nullable()->after('singkatan_sekolah');
            $table->string('website_sekolah')->nullable()->after('alamat_sekolah');
            $table->string('email_sekolah')->nullable()->after('website_sekolah');

            // Theme colors
            $table->string('warna_primary', 7)->default('#7c3aed')->after('email_sekolah');
            $table->string('warna_secondary', 7)->default('#4f46e5')->after('warna_primary');
            $table->string('warna_accent', 7)->default('#d946ef')->after('warna_secondary');

            // Hero section
            $table->string('hero_bg_path')->nullable()->after('warna_accent');
            $table->string('hero_title')->nullable()->after('hero_bg_path');
            $table->string('hero_subtitle')->nullable()->after('hero_title');

            // Footer
            $table->text('footer_text')->nullable()->after('hero_subtitle');
        });

        // Add superadmin role to users enum
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin','panitia','superadmin') NOT NULL DEFAULT 'admin'");
    }

    public function down(): void
    {
        Schema::table('pengaturan_ppdb', function (Blueprint $table) {
            $table->dropColumn([
                'logo_path', 'favicon_path', 'singkatan_sekolah',
                'alamat_sekolah', 'website_sekolah', 'email_sekolah',
                'warna_primary', 'warna_secondary', 'warna_accent',
                'hero_bg_path', 'hero_title', 'hero_subtitle', 'footer_text',
            ]);
        });
    }
};
