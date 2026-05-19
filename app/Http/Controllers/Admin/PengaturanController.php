<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengaturanPpdb;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PengaturanController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Pengaturan', [
            'pengaturan' => PengaturanPpdb::current(),
        ]);
    }

    public function update(Request $request): RedirectResponse
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

        PengaturanPpdb::current()->update($data);

        return back()->with('success', 'Pengaturan PPDB berhasil disimpan.');
    }
}
