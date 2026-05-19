<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\Pendaftar;
use App\Models\PengaturanPpdb;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    /**
     * AI Chat with conversation history support.
     */
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => ['required', 'string', 'max:1000'],
            'history' => ['nullable', 'array', 'max:20'],
            'history.*.role' => ['required_with:history', 'in:user,assistant'],
            'history.*.content' => ['required_with:history', 'string', 'max:2000'],
        ]);

        $apiKey = config('services.anthropic.api_key');

        if (! $apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'AI assistant belum dikonfigurasi.',
            ], 503);
        }

        $pengaturan = PengaturanPpdb::current();
        $jurusan = Jurusan::where('aktif', true)
            ->withCount(['pendaftar as diterima_count' => fn ($q) => $q->where('status', 'diterima')])
            ->get(['id', 'kode', 'nama', 'deskripsi', 'kuota']);

        $jurusanInfo = $jurusan->map(fn ($j) => "- {$j->kode}: {$j->nama} (kuota: {$j->kuota}, sudah diterima: {$j->diterima_count}, sisa: " . max(0, $j->kuota - $j->diterima_count) . ") — {$j->deskripsi}")->implode("\n");

        $stats = [
            'total' => Pendaftar::count(),
            'pending' => Pendaftar::where('status', 'pending')->count(),
            'diterima' => Pendaftar::where('status', 'diterima')->count(),
        ];

        $systemPrompt = <<<PROMPT
Kamu adalah "PPDB AI Assistant" — asisten virtual cerdas untuk {$pengaturan->nama_sekolah}.
Kamu ramah, informatif, dan selalu menjawab dalam Bahasa Indonesia yang sopan.

═══ INFORMASI PPDB ═══
• Nama Sekolah: {$pengaturan->nama_sekolah}
• Tahun Ajaran: {$pengaturan->tahun_ajaran}
• Periode Pendaftaran: {$pengaturan->tanggal_buka?->format('d M Y')} s/d {$pengaturan->tanggal_tutup?->format('d M Y')}
• Status: {($pengaturan->isOpen() ? 'SEDANG DIBUKA' : 'DITUTUP')}
• Contact Person: {$pengaturan->contact_person}
• Total Pendaftar saat ini: {$stats['total']} orang
• Menunggu verifikasi: {$stats['pending']} orang
• Sudah diterima: {$stats['diterima']} orang

═══ SYARAT PENDAFTARAN ═══
{$pengaturan->syarat_pendaftaran}

═══ JURUSAN TERSEDIA ═══
{$jurusanInfo}

═══ CARA MENDAFTAR ═══
1. Buka halaman "Daftar Sekarang" di website
2. Isi formulir: Data Pribadi → Data Akademik → Data Orang Tua → Upload Berkas
3. Klik "Kirim Pendaftaran"
4. Simpan nomor pendaftaran (format: PPDB-YYYY-XXXXX)
5. Gunakan nomor tersebut untuk cek status di halaman "Cek Status"

═══ PANDUAN MENJAWAB ═══
- Jawab singkat, jelas, dan ramah — maksimal 3 paragraf
- Gunakan bullet points jika perlu
- Jika ditanya tentang jurusan tertentu, beri info detail kuota dan deskripsinya
- Jika ditanya kapan pengumuman, cek apakah hasil sudah diumumkan
- Hasil pengumuman: {($pengaturan->hasil_diumumkan ? 'SUDAH diumumkan pada ' . $pengaturan->tanggal_pengumuman?->format('d M Y') : 'BELUM diumumkan')}
- Jika ditanya hal di luar PPDB, jawab singkat lalu arahkan kembali ke PPDB
- JANGAN mengarang informasi yang tidak ada di data di atas
PROMPT;

        // Build messages with history
        $messages = [];
        $history = $request->input('history', []);
        foreach ($history as $msg) {
            $messages[] = [
                'role' => $msg['role'],
                'content' => $msg['content'],
            ];
        }
        $messages[] = ['role' => 'user', 'content' => $request->input('message')];

        try {
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
            ])->timeout(30)->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-haiku-4-5-20251001',
                'max_tokens' => 768,
                'system' => $systemPrompt,
                'messages' => $messages,
            ]);

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghubungi AI assistant.',
                ], 502);
            }

            $body = $response->json();
            $reply = $body['content'][0]['text'] ?? 'Maaf, saya tidak bisa menjawab saat ini.';

            return response()->json([
                'success' => true,
                'data' => [
                    'reply' => $reply,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan pada AI assistant.',
            ], 500);
        }
    }

    /**
     * AI form assistant - help users fill the registration form.
     */
    public function formAssist(Request $request): JsonResponse
    {
        $request->validate([
            'field' => ['required', 'string', 'max:50'],
            'value' => ['nullable', 'string', 'max:500'],
            'context' => ['nullable', 'string', 'max:1000'],
        ]);

        $apiKey = config('services.anthropic.api_key');
        if (! $apiKey) {
            return response()->json(['success' => false, 'message' => 'AI belum dikonfigurasi.'], 503);
        }

        $field = $request->input('field');
        $value = $request->input('value', '');
        $context = $request->input('context', '');

        $systemPrompt = <<<PROMPT
Kamu adalah form assistant untuk pendaftaran PPDB. Tugasmu membantu pengguna mengisi formulir pendaftaran.
Jawab HANYA dalam 1-2 kalimat singkat dalam Bahasa Indonesia.
Jangan gunakan markdown. Jawab langsung tanpa basa-basi.
PROMPT;

        $userPrompt = "Field yang sedang diisi: \"{$field}\"\nNilai saat ini: \"{$value}\"\nKonteks form: {$context}\n\nBerikan tips singkat untuk mengisi field ini dengan benar.";

        try {
            $response = Http::withHeaders([
                'x-api-key' => $apiKey,
                'anthropic-version' => '2023-06-01',
            ])->timeout(15)->post('https://api.anthropic.com/v1/messages', [
                'model' => 'claude-haiku-4-5-20251001',
                'max_tokens' => 128,
                'system' => $systemPrompt,
                'messages' => [['role' => 'user', 'content' => $userPrompt]],
            ]);

            if ($response->failed()) {
                return response()->json(['success' => false], 502);
            }

            return response()->json([
                'success' => true,
                'data' => ['tip' => $response->json('content.0.text', '')],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }
}
