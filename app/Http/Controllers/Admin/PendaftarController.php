<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\Pendaftar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PendaftarController extends Controller
{
    public function index(Request $request): Response
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

        $pendaftar = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('Admin/Pendaftar/Index', [
            'pendaftar' => $pendaftar,
            'jurusan' => Jurusan::orderBy('nama')->get(['id', 'nama']),
            'filters' => $request->only(['search', 'status', 'jurusan_id']),
        ]);
    }

    public function show(Pendaftar $pendaftar): Response
    {
        $pendaftar->load('jurusan');

        return Inertia::render('Admin/Pendaftar/Show', [
            'pendaftar' => $pendaftar,
            'berkas_url' => $pendaftar->berkas_path
                ? Storage::disk('public')->url($pendaftar->berkas_path)
                : null,
        ]);
    }

    public function update(Request $request, Pendaftar $pendaftar): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,diterima,ditolak'],
            'catatan_admin' => ['nullable', 'string'],
        ]);

        $pendaftar->update($data);

        return back()->with('success', 'Status pendaftar berhasil diperbarui.');
    }

    public function destroy(Pendaftar $pendaftar): RedirectResponse
    {
        if ($pendaftar->berkas_path) {
            Storage::disk('public')->delete($pendaftar->berkas_path);
        }
        $pendaftar->delete();

        return redirect()->route('admin.pendaftar.index')
            ->with('success', 'Pendaftar dihapus.');
    }

    public function bulk(Request $request): RedirectResponse
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

        return back()->with('success', $msg);
    }

    public function export(Request $request): StreamedResponse
    {
        $query = Pendaftar::with('jurusan');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($jurusanId = $request->input('jurusan_id')) {
            $query->where('jurusan_id', $jurusanId);
        }
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('no_pendaftaran', 'like', "%{$search}%");
            });
        }

        $filename = 'pendaftar-ppdb-' . date('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            // BOM agar Excel mengenali UTF-8
            fwrite($out, "\xEF\xBB\xBF");

            fputcsv($out, [
                'No. Pendaftaran', 'Nama Lengkap', 'NISN', 'NIK', 'JK',
                'Tempat Lahir', 'Tanggal Lahir', 'Agama', 'Alamat',
                'No. HP', 'Email', 'Asal Sekolah', 'Tahun Lulus', 'Nilai Rata-rata',
                'Nama Ayah', 'Nama Ibu', 'Pekerjaan Ayah', 'Pekerjaan Ibu', 'No. HP Ortu',
                'Jurusan', 'Status', 'Catatan Admin', 'Tanggal Daftar',
            ]);

            $query->orderBy('id')->chunk(200, function ($rows) use ($out) {
                foreach ($rows as $p) {
                    fputcsv($out, [
                        $p->no_pendaftaran,
                        $p->nama_lengkap,
                        $p->nisn,
                        $p->nik,
                        $p->jenis_kelamin,
                        $p->tempat_lahir,
                        optional($p->tanggal_lahir)->format('Y-m-d'),
                        $p->agama,
                        $p->alamat,
                        $p->no_hp,
                        $p->email,
                        $p->asal_sekolah,
                        $p->tahun_lulus,
                        $p->nilai_rata_rata,
                        $p->nama_ayah,
                        $p->nama_ibu,
                        $p->pekerjaan_ayah,
                        $p->pekerjaan_ibu,
                        $p->no_hp_ortu,
                        optional($p->jurusan)->nama,
                        $p->status,
                        $p->catatan_admin,
                        optional($p->created_at)->format('Y-m-d H:i'),
                    ]);
                }
            });

            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
