<?php

namespace App\Http\Controllers;

use App\Models\Jurusan;
use App\Models\Pendaftar;
use App\Models\PengaturanPpdb;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PpdbController extends Controller
{
    public function index(): Response
    {
        $pengaturan = PengaturanPpdb::current();
        $jurusan = Jurusan::where('aktif', true)->orderBy('nama')->get();

        return Inertia::render('Welcome', [
            'pengaturan' => $pengaturan,
            'jurusan' => $jurusan,
            'is_open' => $pengaturan->isOpen(),
            'stats' => [
                'total_pendaftar' => Pendaftar::count(),
                'diterima' => Pendaftar::where('status', 'diterima')->count(),
                'jumlah_jurusan' => $jurusan->count(),
            ],
        ]);
    }

    public function jurusanIndex(): Response
    {
        $jurusan = Jurusan::where('aktif', true)
            ->withCount(['pendaftar as diterima_count' => fn ($q) => $q->where('status', 'diterima')])
            ->orderBy('nama')
            ->get()
            ->map(function (Jurusan $j) {
                return [
                    'id' => $j->id,
                    'kode' => $j->kode,
                    'nama' => $j->nama,
                    'deskripsi' => $j->deskripsi,
                    'kuota' => $j->kuota,
                    'diterima' => $j->diterima_count,
                    'sisa_kuota' => max(0, $j->kuota - $j->diterima_count),
                ];
            });

        return Inertia::render('JurusanPublik', [
            'pengaturan' => PengaturanPpdb::current(),
            'jurusan' => $jurusan,
        ]);
    }

    public function pengumuman(): Response
    {
        $pengaturan = PengaturanPpdb::current();

        $diterima = collect();
        if ($pengaturan->hasil_diumumkan) {
            $diterima = Pendaftar::with('jurusan')
                ->where('status', 'diterima')
                ->orderBy('jurusan_id')
                ->orderBy('nama_lengkap')
                ->get(['id', 'no_pendaftaran', 'nama_lengkap', 'asal_sekolah', 'jurusan_id'])
                ->groupBy(fn ($p) => $p->jurusan->nama)
                ->map(fn ($items, $key) => [
                    'jurusan' => $key,
                    'pendaftar' => $items->map(fn ($p) => [
                        'no_pendaftaran' => $p->no_pendaftaran,
                        'nama_lengkap' => $p->nama_lengkap,
                        'asal_sekolah' => $p->asal_sekolah,
                    ])->values(),
                ])->values();
        }

        return Inertia::render('Pengumuman', [
            'pengaturan' => $pengaturan,
            'diumumkan' => $pengaturan->hasil_diumumkan,
            'tanggal_pengumuman' => $pengaturan->tanggal_pengumuman,
            'diterima_per_jurusan' => $diterima,
        ]);
    }

    public function create(): Response
    {
        $pengaturan = PengaturanPpdb::current();

        if (! $pengaturan->isOpen()) {
            return Inertia::render('Daftar/Closed', [
                'pengaturan' => $pengaturan,
            ]);
        }

        $jurusan = Jurusan::where('aktif', true)
            ->withCount(['pendaftar as diterima_count' => fn ($q) => $q->where('status', 'diterima')])
            ->orderBy('nama')
            ->get()
            ->map(function (Jurusan $j) {
                return [
                    'id' => $j->id,
                    'kode' => $j->kode,
                    'nama' => $j->nama,
                    'kuota' => $j->kuota,
                    'sisa_kuota' => max(0, $j->kuota - $j->diterima_count),
                ];
            });

        return Inertia::render('Daftar/Form', [
            'pengaturan' => $pengaturan,
            'jurusan' => $jurusan,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $pengaturan = PengaturanPpdb::current();

        if (! $pengaturan->isOpen()) {
            return back()->with('error', 'Pendaftaran sedang ditutup.');
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
            'nisn.unique' => 'NISN ini sudah terdaftar. Gunakan fitur "Cek Status" jika kamu sudah pernah daftar.',
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

        return redirect()->route('ppdb.sukses', $pendaftar->no_pendaftaran)
            ->with('success', 'Pendaftaran berhasil dikirim.');
    }

    public function sukses(string $no_pendaftaran): Response
    {
        $pendaftar = Pendaftar::with('jurusan')
            ->where('no_pendaftaran', $no_pendaftaran)
            ->firstOrFail();

        return Inertia::render('Daftar/Sukses', [
            'pendaftar' => $pendaftar,
        ]);
    }

    public function bukti(string $no_pendaftaran): View
    {
        $pendaftar = Pendaftar::with('jurusan')
            ->where('no_pendaftaran', $no_pendaftaran)
            ->firstOrFail();

        return view('bukti-pendaftaran', [
            'pendaftar' => $pendaftar,
            'pengaturan' => PengaturanPpdb::current(),
        ]);
    }

    public function cekStatus(Request $request): Response
    {
        $no = $request->input('no_pendaftaran');
        $pendaftar = null;

        if ($no) {
            $pendaftar = Pendaftar::with('jurusan')
                ->where('no_pendaftaran', $no)
                ->first();
        }

        return Inertia::render('CekStatus', [
            'pendaftar' => $pendaftar,
            'no_pendaftaran' => $no,
            'not_found' => $no && ! $pendaftar,
        ]);
    }
}
