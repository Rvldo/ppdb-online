<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\Pendaftar;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
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
        ])->orderBy('nama')->get();

        $terbaru = Pendaftar::with('jurusan')
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'per_jurusan' => $perJurusan,
            'terbaru' => $terbaru,
        ]);
    }
}
