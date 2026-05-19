<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaturan_ppdb', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sekolah');
            $table->string('tahun_ajaran', 9);
            $table->date('tanggal_buka');
            $table->date('tanggal_tutup');
            $table->boolean('pendaftaran_dibuka')->default(true);
            $table->text('pengumuman')->nullable();
            $table->text('syarat_pendaftaran')->nullable();
            $table->string('contact_person', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaturan_ppdb');
    }
};
