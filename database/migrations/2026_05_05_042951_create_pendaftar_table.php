<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftar', function (Blueprint $table) {
            $table->id();
            $table->string('no_pendaftaran', 20)->unique();
            $table->string('nama_lengkap');
            $table->string('nisn', 20)->nullable();
            $table->string('nik', 20)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir', 100);
            $table->date('tanggal_lahir');
            $table->string('agama', 30);
            $table->text('alamat');
            $table->string('no_hp', 20);
            $table->string('email')->nullable();
            $table->string('asal_sekolah');
            $table->string('tahun_lulus', 4);
            $table->decimal('nilai_rata_rata', 5, 2)->nullable();
            $table->string('nama_ayah');
            $table->string('nama_ibu');
            $table->string('pekerjaan_ayah', 100)->nullable();
            $table->string('pekerjaan_ibu', 100)->nullable();
            $table->string('no_hp_ortu', 20);
            $table->foreignId('jurusan_id')->constrained('jurusan');
            $table->enum('status', ['pending', 'diterima', 'ditolak'])->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->string('berkas_path')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('jurusan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftar');
    }
};
