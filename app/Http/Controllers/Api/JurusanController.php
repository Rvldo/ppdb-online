<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class JurusanController extends Controller
{
    /**
     * Public: List jurusan aktif dengan sisa kuota.
     */
    public function publicIndex(): JsonResponse
    {
        $jurusan = Jurusan::where('aktif', true)
            ->withCount(['pendaftar as diterima_count' => fn ($q) => $q->where('status', 'diterima')])
            ->orderBy('nama')
            ->get()
            ->map(fn (Jurusan $j) => [
                'id' => $j->id,
                'kode' => $j->kode,
                'nama' => $j->nama,
                'deskripsi' => $j->deskripsi,
                'kuota' => $j->kuota,
                'diterima' => $j->diterima_count,
                'sisa_kuota' => max(0, $j->kuota - $j->diterima_count),
            ]);

        return response()->json([
            'success' => true,
            'data' => $jurusan,
        ]);
    }

    /**
     * Public: Detail jurusan.
     */
    public function publicShow(Jurusan $jurusan): JsonResponse
    {
        $jurusan->loadCount(['pendaftar as diterima_count' => fn ($q) => $q->where('status', 'diterima')]);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $jurusan->id,
                'kode' => $jurusan->kode,
                'nama' => $jurusan->nama,
                'deskripsi' => $jurusan->deskripsi,
                'kuota' => $jurusan->kuota,
                'diterima' => $jurusan->diterima_count,
                'sisa_kuota' => max(0, $jurusan->kuota - $jurusan->diterima_count),
            ],
        ]);
    }

    /**
     * Admin: List semua jurusan (termasuk non-aktif).
     */
    public function index(): JsonResponse
    {
        $jurusan = Jurusan::withCount([
            'pendaftar',
            'pendaftar as diterima_count' => fn ($q) => $q->where('status', 'diterima'),
        ])->orderBy('nama')->get();

        return response()->json([
            'success' => true,
            'data' => $jurusan,
        ]);
    }

    /**
     * Admin: Tambah jurusan baru.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'max:10', 'unique:jurusan,kode'],
            'nama' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string'],
            'kuota' => ['required', 'integer', 'min:0'],
            'aktif' => ['boolean'],
        ]);

        $jurusan = Jurusan::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Jurusan berhasil ditambahkan.',
            'data' => $jurusan,
        ], 201);
    }

    /**
     * Admin: Update jurusan.
     */
    public function update(Request $request, Jurusan $jurusan): JsonResponse
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'max:10', Rule::unique('jurusan', 'kode')->ignore($jurusan->id)],
            'nama' => ['required', 'string', 'max:150'],
            'deskripsi' => ['nullable', 'string'],
            'kuota' => ['required', 'integer', 'min:0'],
            'aktif' => ['boolean'],
        ]);

        $jurusan->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Jurusan berhasil diperbarui.',
            'data' => $jurusan,
        ]);
    }

    /**
     * Admin: Hapus jurusan.
     */
    public function destroy(Jurusan $jurusan): JsonResponse
    {
        if ($jurusan->pendaftar()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Jurusan tidak dapat dihapus karena sudah memiliki pendaftar.',
            ], 422);
        }

        $jurusan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jurusan berhasil dihapus.',
        ]);
    }
}
