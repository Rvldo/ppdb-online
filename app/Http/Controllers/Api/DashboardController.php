<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\Pendaftar;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $stats = [
            'total' => Pendaftar::count(),
            'pending' => Pendaftar::where('status', 'pending')->count(),
            'diterima' => Pendaftar::where('status', 'diterima')->count(),
            'ditolak' => Pendaftar::where('status', 'ditolak')->count(),
        ];

        $perJurusan = Jurusan::withCount([
            'pendaftar',
            'pendaftar as diterima_count' => fn ($q) => $q->where('status', 'diterima'),
        ])->orderBy('nama')->get()->map(fn ($j) => [
            'id' => $j->id,
            'kode' => $j->kode,
            'nama' => $j->nama,
            'kuota' => $j->kuota,
            'total_pendaftar' => $j->pendaftar_count,
            'diterima' => $j->diterima_count,
            'sisa_kuota' => max(0, $j->kuota - $j->diterima_count),
        ]);

        $terbaru = Pendaftar::with('jurusan')
            ->latest()
            ->take(10)
            ->get(['id', 'no_pendaftaran', 'nama_lengkap', 'jurusan_id', 'status', 'created_at']);

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'per_jurusan' => $perJurusan,
                'pendaftar_terbaru' => $terbaru,
            ],
        ]);
    }
}
