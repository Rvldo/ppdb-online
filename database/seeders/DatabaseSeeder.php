<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use App\Models\PengaturanPpdb;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'superadmin@ppdb.test'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@ppdb.test'],
            [
                'name' => 'Administrator PPDB',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $jurusan = [
            ['kode' => 'RPL', 'nama' => 'Rekayasa Perangkat Lunak', 'kuota' => 72,
             'deskripsi' => 'Belajar membangun aplikasi web, mobile, dan rekayasa software modern.'],
            ['kode' => 'TKJ', 'nama' => 'Teknik Komputer & Jaringan', 'kuota' => 72,
             'deskripsi' => 'Konsentrasi pada infrastruktur jaringan, server, dan keamanan siber.'],
            ['kode' => 'MM',  'nama' => 'Multimedia', 'kuota' => 36,
             'deskripsi' => 'Desain grafis, animasi, video editing, dan produksi konten kreatif.'],
            ['kode' => 'AKL', 'nama' => 'Akuntansi & Keuangan Lembaga', 'kuota' => 36,
             'deskripsi' => 'Akuntansi perusahaan, perpajakan, dan administrasi keuangan.'],
            ['kode' => 'BDP', 'nama' => 'Bisnis Daring & Pemasaran', 'kuota' => 36,
             'deskripsi' => 'Pemasaran digital, e-commerce, dan strategi bisnis online.'],
        ];

        foreach ($jurusan as $j) {
            Jurusan::updateOrCreate(
                ['kode' => $j['kode']],
                [...$j, 'aktif' => true]
            );
        }

        PengaturanPpdb::updateOrCreate(
            ['id' => 1],
            [
                'nama_sekolah' => 'SMK Karya Bangsa',
                'tahun_ajaran' => date('Y') . '/' . (date('Y') + 1),
                'tanggal_buka' => now()->subDays(7),
                'tanggal_tutup' => now()->addMonths(2),
                'pendaftaran_dibuka' => true,
                'pengumuman' => "Selamat datang di PPDB Online SMK Karya Bangsa.\nSilakan lengkapi formulir pendaftaran dengan benar.",
                'syarat_pendaftaran' => "1. Fotocopy ijazah / Surat Keterangan Lulus\n2. Fotocopy Akte Kelahiran\n3. Fotocopy Kartu Keluarga\n4. Pas foto 3x4 (2 lembar)",
                'contact_person' => '0812-3456-7890',
                'hasil_diumumkan' => false,
                'tanggal_pengumuman' => now()->addMonths(2)->addDays(7),
            ]
        );

        $this->call(DummyPendaftarSeeder::class);
    }
}
