<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class JurusanController extends Controller
{
    public function index(): Response
    {
        $jurusan = Jurusan::withCount([
            'pendaftar',
            'pendaftar as diterima_count' => fn ($q) => $q->where('status', 'diterima'),
        ])->orderBy('nama')->get();

        return Inertia::render('Admin/Jurusan/Index', [
            'jurusan' => $jurusan,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'max:10', 'unique:jurusan,kode'],
            'nama' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string'],
            'kuota' => ['required', 'integer', 'min:0'],
            'aktif' => ['boolean'],
        ]);

        Jurusan::create($data);

        return back()->with('success', 'Jurusan berhasil ditambahkan.');
    }

    public function update(Request $request, Jurusan $jurusan): RedirectResponse
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'max:10', Rule::unique('jurusan', 'kode')->ignore($jurusan->id)],
            'nama' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string'],
            'kuota' => ['required', 'integer', 'min:0'],
            'aktif' => ['boolean'],
        ]);

        $jurusan->update($data);

        return back()->with('success', 'Jurusan berhasil diperbarui.');
    }

    public function destroy(Jurusan $jurusan): RedirectResponse
    {
        if ($jurusan->pendaftar()->exists()) {
            return back()->with('error', 'Jurusan tidak dapat dihapus karena sudah memiliki pendaftar.');
        }

        $jurusan->delete();

        return back()->with('success', 'Jurusan dihapus.');
    }
}
