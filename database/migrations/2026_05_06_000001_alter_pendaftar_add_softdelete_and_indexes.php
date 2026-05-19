<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pendaftar', function (Blueprint $table) {
            $table->softDeletes();
            $table->unique('nisn', 'pendaftar_nisn_unique');
            $table->index('no_pendaftaran', 'pendaftar_nopendaftaran_idx');
        });

        Schema::table('pengaturan_ppdb', function (Blueprint $table) {
            $table->boolean('hasil_diumumkan')->default(false)->after('contact_person');
            $table->date('tanggal_pengumuman')->nullable()->after('hasil_diumumkan');
        });
    }

    public function down(): void
    {
        Schema::table('pendaftar', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropUnique('pendaftar_nisn_unique');
            $table->dropIndex('pendaftar_nopendaftaran_idx');
        });

        Schema::table('pengaturan_ppdb', function (Blueprint $table) {
            $table->dropColumn(['hasil_diumumkan', 'tanggal_pengumuman']);
        });
    }
};
