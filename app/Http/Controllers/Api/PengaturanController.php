<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PengaturanPpdb;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PengaturanController extends Controller
{
    /**
     * Public: Informasi umum PPDB.
     */
    public function publicInfo(): JsonResponse
    {
        $pengaturan = PengaturanPpdb::current();

        return response()->json([
            'success' => true,
            'data' => [
                'nama_sekolah' => $pengaturan->nama_sekolah,
                'tahun_ajaran' => $pengaturan->tahun_ajaran,
                'tanggal_buka' => $pengaturan->tanggal_buka?->format('Y-m-d'),
                'tanggal_tutup' => $pengaturan->tanggal_tutup?->format('Y-m-d'),
                'pendaftaran_dibuka' => $pengaturan->isOpen(),
                'pengumuman' => $pengaturan->pengumuman,
                'syarat_pendaftaran' => $pengaturan->syarat_pendaftaran,
                'contact_person' => $pengaturan->contact_person,
                'hasil_diumumkan' => $pengaturan->hasil_diumumkan,
                'tanggal_pengumuman' => $pengaturan->tanggal_pengumuman?->format('Y-m-d'),
            ],
        ]);
    }

    /**
     * Public: Data pengumuman hasil seleksi.
     */
    public function pengumuman(): JsonResponse
    {
        $pengaturan = PengaturanPpdb::current();

        if (! $pengaturan->hasil_diumumkan) {
            return response()->json([
                'success' => true,
                'data' => [
                    'diumumkan' => false,
                    'message' => 'Hasil seleksi belum diumumkan.',
                ],
            ]);
        }

        $diterima = \App\Models\Pendaftar::with('jurusan')
            ->where('status', 'diterima')
            ->orderBy('jurusan_id')
            ->orderBy('nama_lengkap')
            ->get(['id', 'no_pendaftaran', 'nama_lengkap', 'asal_sekolah', 'jurusan_id'])
            ->groupBy(fn ($p) => $p->jurusan->nama)
            ->map(fn ($items, $key) => [
                'jurusan' => $key,
                'jumlah' => $items->count(),
                'pendaftar' => $items->map(fn ($p) => [
                    'no_pendaftaran' => $p->no_pendaftaran,
                    'nama_lengkap' => $p->nama_lengkap,
                    'asal_sekolah' => $p->asal_sekolah,
                ])->values(),
            ])->values();

        return response()->json([
            'success' => true,
            'data' => [
                'diumumkan' => true,
                'tanggal_pengumuman' => $pengaturan->tanggal_pengumuman?->format('Y-m-d'),
                'diterima_per_jurusan' => $diterima,
            ],
        ]);
    }

    /**
     * Admin: Get full pengaturan.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => PengaturanPpdb::current(),
        ]);
    }

    /**
     * Admin: Update pengaturan.
     */
    public function update(Request $request): JsonResponse
    {
        $data = $request->validate([
            'nama_sekolah' => ['required', 'string', 'max:150'],
            'tahun_ajaran' => ['required', 'string', 'max:9'],
            'tanggal_buka' => ['required', 'date'],
            'tanggal_tutup' => ['required', 'date', 'after_or_equal:tanggal_buka'],
            'pendaftaran_dibuka' => ['boolean'],
            'pengumuman' => ['nullable', 'string'],
            'syarat_pendaftaran' => ['nullable', 'string'],
            'contact_person' => ['nullable', 'string', 'max:100'],
            'hasil_diumumkan' => ['boolean'],
            'tanggal_pengumuman' => ['nullable', 'date'],
        ]);

        $pengaturan = PengaturanPpdb::current();
        $pengaturan->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Pengaturan PPDB berhasil disimpan.',
            'data' => $pengaturan->fresh(),
        ]);
    }
}
