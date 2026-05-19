<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\Pendaftar;
use App\Models\PengaturanPpdb;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PendaftarController extends Controller
{
    /**
     * Public: Submit pendaftaran baru.
     */
    public function store(Request $request): JsonResponse
    {
        $pengaturan = PengaturanPpdb::current();

        if (! $pengaturan->isOpen()) {
            return response()->json([
                'success' => false,
                'message' => 'Pendaftaran sedang ditutup.',
            ], 403);
        }

        $data = $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:150'],
            'nisn' => ['nullable', 'string', 'max:20', Rule::unique('pendaftar', 'nisn')->whereNull('deleted_at')],
            'nik' => ['nullable', 'string', 'max:20'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'tempat_lahir' => ['required', 'string', 'max:100'],
            'tanggal_lahir' => ['required', 'date', 'before:today'],
            'agama' => ['required', 'string', 'max:30'],
            'alamat' => ['required', 'string'],
            'no_hp' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'asal_sekolah' => ['required', 'string', 'max:150'],
            'tahun_lulus' => ['required', 'digits:4'],
            'nilai_rata_rata' => ['nullable', 'numeric', 'between:0,100'],
            'nama_ayah' => ['required', 'string', 'max:150'],
            'nama_ibu' => ['required', 'string', 'max:150'],
            'pekerjaan_ayah' => ['nullable', 'string', 'max:100'],
            'pekerjaan_ibu' => ['nullable', 'string', 'max:100'],
            'no_hp_ortu' => ['required', 'string', 'max:20'],
            'jurusan_id' => ['required', 'exists:jurusan,id'],
            'berkas' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ], [
            'nisn.unique' => 'NISN ini sudah terdaftar.',
        ]);

        $jurusan = Jurusan::findOrFail($data['jurusan_id']);
        if (! $jurusan->aktif) {
            throw ValidationException::withMessages([
                'jurusan_id' => 'Jurusan ini sudah tidak menerima pendaftar.',
            ]);
        }

        $pendaftar = DB::transaction(function () use ($request, $data) {
            $berkasPath = null;
            if ($request->hasFile('berkas')) {
                $berkasPath = $request->file('berkas')->store('berkas', 'public');
            }

            return Pendaftar::create([
                ...$data,
                'no_pendaftaran' => Pendaftar::generateNoPendaftaran(),
                'status' => 'pending',
                'berkas_path' => $berkasPath,
            ]);
        });

        $pendaftar->load('jurusan');

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran berhasil dikirim.',
            'data' => $pendaftar,
        ], 201);
    }

    /**
     * Public: Cek status pendaftaran.
     */
    public function cekStatus(string $noPendaftaran): JsonResponse
    {
        $pendaftar = Pendaftar::with('jurusan')
            ->where('no_pendaftaran', $noPendaftaran)
            ->first();

        if (! $pendaftar) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor pendaftaran tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'no_pendaftaran' => $pendaftar->no_pendaftaran,
                'nama_lengkap' => $pendaftar->nama_lengkap,
                'jurusan' => $pendaftar->jurusan->nama ?? null,
                'status' => $pendaftar->status,
                'catatan_admin' => $pendaftar->catatan_admin,
                'tanggal_daftar' => $pendaftar->created_at->format('Y-m-d H:i:s'),
            ],
        ]);
    }

    /**
     * Admin: List semua pendaftar dengan filter & pagination.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Pendaftar::with('jurusan');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('no_pendaftaran', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('asal_sekolah', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($jurusanId = $request->input('jurusan_id')) {
            $query->where('jurusan_id', $jurusanId);
        }

        $perPage = min((int) $request->input('per_page', 15), 100);
        $pendaftar = $query->latest()->paginate($perPage)->withQueryString();

        return response()->json([
            'success' => true,
            'data' => $pendaftar,
        ]);
    }

    /**
     * Admin: Detail pendaftar.
     */
    public function show(Pendaftar $pendaftar): JsonResponse
    {
        $pendaftar->load('jurusan');

        return response()->json([
            'success' => true,
            'data' => [
                ...$pendaftar->toArray(),
                'berkas_url' => $pendaftar->berkas_path
                    ? Storage::disk('public')->url($pendaftar->berkas_path)
                    : null,
            ],
        ]);
    }

    /**
     * Admin: Update status pendaftar.
     */
    public function update(Request $request, Pendaftar $pendaftar): JsonResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,diterima,ditolak'],
            'catatan_admin' => ['nullable', 'string'],
        ]);

        $pendaftar->update($data);
        $pendaftar->load('jurusan');

        return response()->json([
            'success' => true,
            'message' => 'Status pendaftar berhasil diperbarui.',
            'data' => $pendaftar,
        ]);
    }

    /**
     * Admin: Hapus pendaftar.
     */
    public function destroy(Pendaftar $pendaftar): JsonResponse
    {
        if ($pendaftar->berkas_path) {
            Storage::disk('public')->delete($pendaftar->berkas_path);
        }
        $pendaftar->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pendaftar berhasil dihapus.',
        ]);
    }

    /**
     * Admin: Bulk action (terima/tolak/reset/hapus).
     */
    public function bulk(Request $request): JsonResponse
    {
        $data = $request->validate([
            'action' => ['required', 'in:terima,tolak,reset,hapus'],
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:pendaftar,id'],
        ]);

        $query = Pendaftar::whereIn('id', $data['ids']);
        $count = 0;
        $msg = '';

        switch ($data['action']) {
            case 'terima':
                $count = $query->update(['status' => 'diterima']);
                $msg = "{$count} pendaftar diterima.";
                break;
            case 'tolak':
                $count = $query->update(['status' => 'ditolak']);
                $msg = "{$count} pendaftar ditolak.";
                break;
            case 'reset':
                $count = $query->update(['status' => 'pending']);
                $msg = "{$count} pendaftar dikembalikan ke status pending.";
                break;
            case 'hapus':
                foreach ($query->get() as $row) {
                    if ($row->berkas_path) {
                        Storage::disk('public')->delete($row->berkas_path);
                    }
                    $row->delete();
                    $count++;
                }
                $msg = "{$count} pendaftar dihapus.";
                break;
        }

        return response()->json([
            'success' => true,
            'message' => $msg,
            'affected' => $count,
        ]);
    }
}
